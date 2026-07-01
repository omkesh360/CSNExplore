<?php

/**
 * Core Plugin Class
 *
 * This class serves as the central core of the W3speedster plugin, providing
 * essential functionality for settings management, caching, optimization,
 * and various utility methods. It handles plugin initialization, hooks,
 * and core operations.
 *
 * @package W3speedster
 * @since 9.0.0
 * @author W3speedster Team
 */

namespace W3speedster;

use W3speedster\W3DB;

/**
 * Core Plugin Class
 *
 * Handles core plugin functionality including settings management,
 * caching operations, optimization features, and utility methods.
 * This class is the foundation for all other plugin components.
 */
class Functions {
    public $w3db;

    public $source;

    public function __construct(){
        if(!empty(W3SPEEDSTER_CONFIG['storage_type']) && W3SPEEDSTER_CONFIG['storage_type'] == 'database'){
            $this->w3db = W3DB::getInstance()->connect();
            $this->source = 'db';
        }else{
            $this->source = 'file';
        }

    }

    /**
     * Get Web Vitals log data (from DB or file)
     *
     * Retrieves and formats Web Vitals performance data from the database or file
     * with filtering and pagination support.
     *
     * @param string $source 'db' or 'file' (default: 'db' if DB is available, otherwise 'file')
     * @return string HTML table with log data and pagination
     */
    function w3SpeedsterGetLogData()
    {
        // Common filter and pagination params
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        $offset = ($paged - 1) * $limit;

        $issuetype = isset($_POST['issuetype']) ? $_POST['issuetype'] : '';
        $deviceType = isset($_POST['deviceType']) ? $_POST['deviceType'] : '';
        $urls = isset($_POST['url']) ? $_POST['url'] : '';
        $startDate = !empty($_POST['start_date']) ? strtotime($_POST['start_date']) : null;
        $endDate = !empty($_POST['end_date']) ? strtotime($_POST['end_date']) + 86400 - 1 : null; // end of day

        $logResult = [];
        $totalRows = 0;
        $page = 1;

        if ($this->source === 'db' && $this->w3db) {
            // --- Database Mode ---
            $table_name = 'w3_core_webvitals';
            $conditions = array();
            $params = array();

            if (!empty($issuetype)) {
                $conditions[] = "issuetype = :issuetype";
                $params[':issuetype'] = $issuetype;
            }
            if (!empty($deviceType)) {
                $conditions[] = "deviceType = :deviceType";
                $params[':deviceType'] = $deviceType;
            }
            if (!empty($urls)) {
                $url_conditions = array();
                if (is_array($urls)) {
                    foreach ($urls as $idx => $url) {
                        $paramName = ":url" . $idx;
                        $url_conditions[] = "url = $paramName";
                        $params[$paramName] = $url;
                    }
                } else {
                    $url_conditions[] = "url = :url";
                    $params[':url'] = $urls;
                }
                $conditions[] = '(' . implode(" OR ", $url_conditions) . ')';
            }
            if (!empty($startDate) && !empty($endDate)) {
                $conditions[] = "UNIX_TIMESTAMP(timestamp) BETWEEN :start_date AND :end_date";
                $params[':start_date'] = $startDate;
                $params[':end_date'] = $endDate;
            }

            $where = '';
            if (!empty($conditions)) {
                $where = " WHERE " . implode(" AND ", $conditions);
            }

            // Get total count for pagination
            $count_sql = "SELECT COUNT(*) as total FROM $table_name" . $where;
            $stmt_count = $this->w3db->prepare($count_sql);
            $stmt_count->execute($params);
            $totalRows = $stmt_count->fetchColumn();
            if ($totalRows < $limit) {
                $offset = 0;
            }
            $page = $totalRows > 0 ? ceil($totalRows / $limit) : 1;

            // Get paginated results
            $sql = "SELECT * FROM $table_name" . $where . " ORDER BY id DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->w3db->prepare($sql);

            // Bind params for filtering
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            // Bind limit/offset as integers
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);

            $stmt->execute();
            $logResult = $stmt->fetchAll(\PDO::FETCH_OBJ);

        } else {
            // --- File Mode ---
            $jsonFilePath = W3SPEEDSTER_PATH . '/data/webvitals.json';
            $webvitals = [];
            if ($jsonFilePath && file_exists($jsonFilePath)) {
                $webvitals = json_decode(file_get_contents($jsonFilePath), true) ?? [];
            }

            $filteredData = array_filter($webvitals, function ($entry) use ($issuetype, $urls, $deviceType, $startDate, $endDate) {
                $entryTimestamp = strtotime($entry['timestamp']);
                if (!empty($issuetype) && $entry['issuetype'] !== $issuetype) return false;
                if (!empty($urls)) {
                    if (is_array($urls)) {
                        if (!in_array($entry['url'], $urls)) return false;
                    } else {
                        if ($entry['url'] !== $urls) return false;
                    }
                }
                if (!empty($deviceType) && $entry['deviceType'] !== $deviceType) return false;
                if (!empty($startDate) && $entryTimestamp < $startDate) return false;
                if (!empty($endDate) && $entryTimestamp > $endDate) return false;
                return true;
            });

            $totalRows = count($filteredData);
            $page = $totalRows > 0 ? ceil($totalRows / $limit) : 1;
            $logResult = array_slice(array_values($filteredData), $offset, $limit);
        }

        // --- Output HTML Table and Pagination ---
        $logData = '';
        if ($logResult && count($logResult) > 0) {
            $logData .= '<table class="webvitals-table"><thead><th>ID</th><th>Url</th><th>Issue Type</th><th>Device Type</th><th>Data</th><th>Time</th></thead><tbody>';
            foreach ($logResult as $entry) {
                // DB returns object, file returns array
                $id = is_object($entry) ? $entry->id : $entry['id'];
                $url = is_object($entry) ? $entry->url : $entry['url'];
                $issuetypeVal = is_object($entry) ? $entry->issuetype : $entry['issuetype'];
                $deviceTypeVal = is_object($entry) ? $entry->deviceType : $entry['deviceType'];
                $dataVal = is_object($entry) ? $entry->data : $entry['data'];
                $timestampVal = is_object($entry) ? $entry->timestamp : $entry['timestamp'];

                $prettyJsonString = json_encode(json_decode(stripslashes($dataVal), true), JSON_PRETTY_PRINT);
                $logData .= '<tr><td class="id_' . $id . '">' . $id . '</td><td class="url url_' . $id . '">' . urldecode($url) . '</td><td class="issueType_' . $id . '">' . $issuetypeVal . '</td><td class="deviceType_' . $id . '">' . $deviceTypeVal . '</td><td class="data_' . $id . '"><div class="log-data">' . $prettyJsonString . '</div><button data-id="' . $id . '" class="more_info" type="button" popovertarget="more_info" popovertargetaction="show">View More</button></td><td class="time_' . $id . '">' . $timestampVal . '</td></tr>';
            }
            $logData .= '</tbody></table><div class="pagination" data-last="' . $page . '">';
            if ($paged > 1) {
                $logData .= '<button type="button" class="page-prev" data-page="1"><<</button><button type="button" class="page-prev" data-page="' . ($paged - 1) . '"><</button>';
            }
            for ($i = 1; $i <= $page; $i++) {
                $activeClass = $i == $paged ? 'active' : '';
                if (($paged <= 2 && $i <= 5) || ($paged >= 3 && $i >= ($paged - 2) && $i <= ($paged + 2))) {
                    $logData .= '<button type="button" class="p-num ' . $activeClass . '" data-page="' . $i . '">' . $i . '</button>';
                }
            }
            if ($paged < $page) {
                $logData .= '<button type="button" class="page-next" data-page="' . ($paged + 1) . '">></button><button type="button" class="page-next-last" data-page="' . $page . '">>></button>';
            }
            $logData .= '</div>';
        } else {
            $logData = '<div class="no-data-found-log">No Data Found</div>';
        }
		return $logData;
    }

    /**
	 * Get settings change log data
	 *
	 * Retrieves and formats settings change log data from the database or file
	 * with filtering and pagination support.
	 *
	 * @return string HTML table with change log data and pagination
	 */
	function w3SpeedsterGetChangeLogData(){
		// Common filter and pagination params
		$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
		$paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
		$offset = ($paged - 1) * $limit;

		$startDate = !empty($_POST['start_date']) ? strtotime($_POST['start_date']) : null;
		$endDate = !empty($_POST['end_date']) ? strtotime($_POST['end_date']) + 86400 - 1 : null; // end of day

		$logResult = [];
		$totalRows = 0;
		$page = 1;

		if ($this->source === 'db' && $this->w3db) {
			// --- Database Mode ---
			$w3db = $this->w3db;
			$table_name = 'w3_change_logs';

			$where = [];
			$params = [];

			if (!empty($startDate) && !empty($endDate)) {
				$where[] = "UNIX_TIMESTAMP(time) BETWEEN :start_date AND :end_date";
				$params[':start_date'] = $startDate;
				$params[':end_date'] = $endDate;
			}

			$where_sql = '';
			if (!empty($where)) {
				$where_sql = ' WHERE ' . implode(' AND ', $where);
			}

			// Get total count for pagination
			$count_sql = "SELECT COUNT(*) as total FROM {$table_name}{$where_sql}";
			$count_stmt = $w3db->prepare($count_sql);
			foreach ($params as $key => $val) {
				$count_stmt->bindValue($key, $val, \PDO::PARAM_INT);
			}
			$count_stmt->execute();
			$totalRows = (int)($count_stmt->fetch()['total'] ?? 0);

			if ($totalRows < $limit) {
				$offset = 0;
			}
			$page = $totalRows > 0 ? ceil($totalRows / $limit) : 1;

			// Get paginated results
			$sql = "SELECT * FROM {$table_name}{$where_sql} ORDER BY id DESC LIMIT :limit OFFSET :offset";
			$stmt = $w3db->prepare($sql);

			// Bind params for where
			foreach ($params as $key => $val) {
				$stmt->bindValue($key, $val, \PDO::PARAM_INT);
			}
			$stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
			$stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);

			$stmt->execute();
			$logResult = $stmt->fetchAll();

		} else {
			// --- File Mode ---
			$jsonFilePath = W3SPEEDSTER_PATH . '/data/changeLogs.json';
			$changeLogs = [];
			if ($jsonFilePath && file_exists($jsonFilePath)) {
				$changeLogs = json_decode(file_get_contents($jsonFilePath), true) ?? [];
			}

			$filteredData = array_filter($changeLogs, function ($entry) use ($startDate, $endDate) {
				$entryTimestamp = strtotime($entry['time']);
				if (!empty($startDate) && $entryTimestamp < $startDate) return false;
				if (!empty($endDate) && $entryTimestamp > $endDate) return false;
				return true;
			});

			$filteredData = array_reverse($filteredData);

			$totalRows = count($filteredData);
			$page = $totalRows > 0 ? ceil($totalRows / $limit) : 1;
			$logResult = array_slice($filteredData, $offset, $limit);
		}

		// --- Output HTML Table and Pagination ---
		$logData = '';
		if ($logResult && count($logResult) > 0) {
			$logData .= '<table class="w3changelogs-table"><thead><th>ID</th><th>Time</th><th>User</th><th>Ip</th><th>Action</th><th>Previous</th><th>New</th></thead><tbody>';
			foreach ($logResult as $entry) {
				// DB returns array, file returns array
				$id = $entry['id'];
				$time = $entry['time'];
				$user = $entry['user'];
				$ip = $entry['ip'];
				$action = $entry['action'];
				$old = $entry['old'];
				$new = $entry['new'];

				$oldContent = (strlen($old) > 20) ? substr($old, 0, 20) . '...' : $old;
				$newContent = (strlen($new) > 20) ? substr($new, 0, 20) . '...' : $new;
				$oldShowMoreButton = (strlen($old) > 20) ? '<button class="show-more" type="button" data-target="old_' . $id . '">Show More</button>' : '';
				$newShowMoreButton = (strlen($new) > 20) ? '<button class="show-more" type="button" data-target="new_' . $id . '">Show More</button><button class="show-diff" type="button" data-target-new="new_' . $id . '" data-target-old="old_' . $id . '">Show Difference</button>' : '';
				$logData .= '<tr>
								<td class="id_' . $id . '">' . $id . '</td>
								<td class="time_' . $id . '">' . $time . '</td>
								<td class="time_' . $id . '">' . $user . '</td>
								<td class="time_' . $id . '">' . $ip . '</td>
								<td class="time_' . $id . '">' . $action . '</td>
								<td class="time_' . $id . '">
									<div id="old_' . $id . '" style="display:none;"><pre>' . $old . '</pre></div>
									<div class="old-content"><pre class="change-log-pre">' . $oldContent . '</pre></div>
									' . $oldShowMoreButton . '
								</td>
								<td class="time_' . $id . '">
									<div id="new_' . $id . '" style="display:none;"><pre>' . $new . '</pre></div>
									<div class="new-content" id="new_' . $id . '"><pre class="change-log-pre">' . $newContent . '</pre></div>
									' . $newShowMoreButton . '
								</td>
							</tr>';
			}

			$logData .= '</tbody></table><div class="pagination" data-last="' . $page . '">';
			if ($paged > 1) {
				$logData .= '<button type="button" class="change-page-prev" data-page="1"><<</button><button type="button" class="change-page-prev" data-page="' . ($paged - 1) . '"><</button>';
			}
			for ($i = 1; $i <= $page; $i++) {
				$activeClass = $i == $paged ? 'active' : '';
				if (($paged <= 2 && $i <= 5) || ($paged >= 3 && $i >= ($paged - 2) && $i <= ($paged + 2))) {
					$logData .= '<button type="button" class="change-p-num ' . $activeClass . '" data-page="' . $i . '">' . $i . '</button>';
				}
			}
			if ($paged < $page) {
				$logData .= '<button type="button" class="change-page-next" data-page="' . ($paged + 1) . '">></button><button type="button" class="change-page-next-last" data-page="' . $page . '">>></button>';
			}
			$logData .= '</div>';
		} else {
			$logData = '<div class="no-data-found-log">No Data Found</div>';
		}

		return $logData;
	}


    /**
	 * Log settings changes
	 *
	 * Records changes to plugin settings in the database or file
	 * for audit and debugging purposes, depending on the source.
	 *
	 * @param array $changes Array of changes with old/new values
	 */
	function logSettingsChanges($changes) {
		$time = date('Y-m-d H:i:s');
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$currentUser = '';
		// If not found, fallback to property if available
		if (empty($currentUser) && property_exists($this, 'loggedInUser')) {
			$currentUser = $this->loggedInUser;
		}

		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_change_logs';
			foreach ($changes as $change) {
				$sql = "INSERT INTO $table_name (`time`, `user`, `ip`, `action`, `old`, `new`) VALUES (:time, :user, :ip, :action, :old, :new)";
				$params = [
					':time'   => $time,
					':user'   => $currentUser,
					':ip'     => $ip,
					':action' => $change['action'],
					':old'    => is_array($change['old']) ? json_encode($change['old'], JSON_PRETTY_PRINT) : $change['old'],
					':new'    => is_array($change['new']) ? json_encode($change['new'], JSON_PRETTY_PRINT) : $change['new']
				];
				$stmt = $w3db->prepare($sql);
				$stmt->execute($params);
			}
		} else {
			$jsonFilePath = W3SPEEDSTER_PATH . '/data/changeLogs.json';
			$changeLogs = [];
			if (file_exists($jsonFilePath)) {
				$changeLogs = json_decode(file_get_contents($jsonFilePath), true) ?? [];
			}
			$maxId = count($changeLogs) + 1;
			foreach ($changes as $change) {
				$changeLogs[] = [
					'id'     => $maxId,
					'time'   => $time,
					'user'   => $currentUser,
					'ip'     => $ip,
					'action' => $change['action'],
					'old'    => is_array($change['old']) ? json_encode($change['old'], JSON_PRETTY_PRINT) : $change['old'],
					'new'    => is_array($change['new']) ? json_encode($change['new'], JSON_PRETTY_PRINT) : $change['new']
				];
				++$maxId;
			}
			$this->w3speedsterPutContents($jsonFilePath, json_encode($changeLogs));
		}
	}

    /**
	 * Get AI optimization table data (DB or JSON)
	 *
	 * Retrieves and formats AI optimization data from the database or JSON file
	 * with filtering, pagination, and status indicators.
	 *
	 * @param array $data Request parameters including pagination and filters
	 * @return array Table data with HTML, pagination info, and statistics
	 */
	function w3GetOptimizeAiTableData($data) {
		// Common input parsing
		$rowsPerPage = isset($data['rows']) ? (int)$data['rows'] : 10;
		$page = isset($data['page']) ? (int)$data['page'] : 1;
		$statusFilter = isset($data['status']) ? (array)$data['status'] : [];
		$url = isset($data['url']) ? trim($data['url']) : '';
		$page = max($page, 1);
		$offset = ($page - 1) * $rowsPerPage;

		$table_html = '';
		$total_rows = 0;
		$total_pages = 1;
		$results = [];
		$total_rows_all = 0;
		$optimized_count = 0;

		if ($this->source === 'db' && $this->w3db) {
			// Database mode
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';

			// Build WHERE clause and params
			$whereSQL = [];
			$whereParams = [];

			if (!empty($statusFilter) && is_array($statusFilter)) {
				$statusPlaceholders = implode(',', array_fill(0, count($statusFilter), '?'));
				$whereSQL[] = "status IN ($statusPlaceholders)";
				$whereParams = array_merge($whereParams, $statusFilter);
			}

			if ($url !== '') {
				if (isset($this->addSettings['siteUrl']) && $this->addSettings['siteUrl'] === rtrim($url, '/')) {
					$whereSQL[] = "url = '/'";
				} else {
					$urlStripped = isset($this->addSettings['siteUrl']) ? str_replace($this->addSettings['siteUrl'], '', $url) : $url;
					$whereSQL[] = "url LIKE ?";
					$whereParams[] = '%' . $urlStripped . '%';
				}
			}

			$whereClause = '';
			if (!empty($whereSQL)) {
				$whereClause = 'WHERE ' . implode(' AND ', $whereSQL);
			}

			// Get total rows for pagination (filtered)
			$count_sql = "SELECT COUNT(*) FROM $table_name $whereClause";
			$count_stmt = $w3db->prepare($count_sql);
			foreach ($whereParams as $idx => $param) {
				$count_stmt->bindValue($idx + 1, $param, is_int($param) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
			}
			$count_stmt->execute();
			$total_rows = (int) $count_stmt->fetchColumn();
			$total_pages = $rowsPerPage > 0 ? ceil($total_rows / $rowsPerPage) : 1;

			// Get paginated results
			$sql = "SELECT * FROM $table_name $whereClause ORDER BY updated_at DESC LIMIT ? OFFSET ?";
			$bindParams = array_merge($whereParams, [$rowsPerPage, $offset]);
			$stmt = $w3db->prepare($sql);
			foreach ($bindParams as $idx => $param) {
				$stmt->bindValue($idx + 1, $param, is_int($param) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
			}
			$stmt->execute();
			$results = $stmt->fetchAll(\PDO::FETCH_OBJ);

			// For stats (unfiltered)
			$total_rows_stmt = $w3db->prepare("SELECT COUNT(*) FROM $table_name");
			$total_rows_stmt->execute();
			$total_rows_all = (int) $total_rows_stmt->fetchColumn();

			$optimized_count_stmt = $w3db->prepare("SELECT COUNT(*) FROM $table_name WHERE status IN (?, ?)");
			$optimized_count_stmt->bindValue(1, 4, \PDO::PARAM_INT);
			$optimized_count_stmt->bindValue(2, 6, \PDO::PARAM_INT);
			$optimized_count_stmt->execute();
			$optimized_count = (int) $optimized_count_stmt->fetchColumn();
		} else {
			// JSON mode
			$jsonFilePath = W3SPEEDSTER_PATH . '/data/w3siteurls.json';
			if (!file_exists($jsonFilePath)) {
				return [
					'html' => '<p>No data file found.</p>',
					'current_page' => 1,
					'total_pages' => 0,
					'optimized_rows' => 0,
					'total_rows' => 0,
					'percentage' => 0,
					'time' => '0 minutes remaining'
				];
			}
			$jsonData = json_decode(file_get_contents($jsonFilePath), true);
			if (!is_array($jsonData)) $jsonData = [];

			// Filtering
			$results = array_filter($jsonData, function ($row) use ($statusFilter, $url) {
				if (!in_array((int)$row['status'], $statusFilter)) return false;

				if ($url !== '') {
					$checkUrl = $row['url'];
					if (isset($this->addSettings['siteUrl']) && rtrim($url, '/') === $this->addSettings['siteUrl']) {
						if ($checkUrl !== '/') return false;
					} else {
						$urlWithoutBase = isset($this->addSettings['siteUrl']) ? str_replace($this->addSettings['siteUrl'], '', $url) : $url;
						if (stripos($checkUrl, $urlWithoutBase) === false) return false;
					}
				}
				return true;
			});

			// Total rows after filtering
			$total_rows = count($results);
			$total_pages = $rowsPerPage > 0 ? ceil($total_rows / $rowsPerPage) : 1;

			// Sort by updated_at DESC
			usort($results, function ($a, $b) {
				return strtotime($b['updated_at']) - strtotime($a['updated_at']);
			});

			// Pagination
			$results = array_slice($results, $offset, $rowsPerPage);

			// For stats (unfiltered)
			$total_rows_all = count($jsonData);
			$optimized_count = count(array_filter($jsonData, fn($r) => in_array((int)$r['status'], [4, 6])));
		}

		// Pagination HTML setup
		$max_pages_each_side = 1;
		$start_page = max(1, $page - $max_pages_each_side);
		$end_page = min($total_pages, $page + $max_pages_each_side);

		// Build Table HTML
		$table_html = '<table class="optimize-url-table">';
		$table_html .= '
			<thead>
				<tr>
					<th>URL</th>
					<th style="width: 10%;">Desktop</th>
					<th style="width: 10%;">Mobile</th>
					<th style="width: 12%;">Timestamp</th>
					<th style="width: 10%;">Actions</th>
				</tr>
			</thead>
			<tbody>';

		if (!empty($results)) {
			foreach ($results as $row) {
				if ($this->source === 'db' && $this->w3db) {
					// DB object
					$urlAbs = $this->w3ChangeUrlRelativeToAbsolute($row->url);
					$id = $row->id;
					$status = (int)$row->status;
					$updated_at = htmlspecialchars($row->updated_at);
				} else {
					// JSON array
					$urlAbs = isset($this->addSettings['siteUrl']) ? rtrim($this->addSettings['siteUrl'], '/') . $row['url'] : $row['url'];
					$id = $row['id'];
					$status = (int)$row['status'];
					$updated_at = htmlspecialchars($row['updated_at']);
				}
				$short_url = strlen($urlAbs) > 100 ? substr($urlAbs, 0, 100) . '...' : $urlAbs;

				switch ($status) {
					case 0:
						$badgeMobClass = $badgeDeskClass = 'pending';
						$badgeMobLabel = $badgeDeskLabel = '<i class="fa fa-hourglass-o" aria-hidden="true"></i>';
						break;
					case 1:
						$badgeMobClass = $badgeDeskClass = 'inProgress';
						$badgeMobLabel = $badgeDeskLabel = '<div class="dots"></div>';
						break;
					case 2:
						$badgeDeskClass = 'optimized';
						$badgeDeskLabel = '<i class="fa fa-check" aria-hidden="true"></i>';
						$badgeMobClass = 'inProgress';
						$badgeMobLabel = '<div class="dots"></div>';
						break;
					case 3:
						$badgeMobClass = 'optimized';
						$badgeMobLabel = '<i class="fa fa-check" aria-hidden="true"></i>';
						$badgeDeskClass = 'inProgress';
						$badgeDeskLabel = '<div class="dots"></div>';
						break;
					case 4:
					case 6:
						$badgeMobClass = $badgeDeskClass = 'optimized';
						$badgeMobLabel = $badgeDeskLabel = '<i class="fa fa-check" aria-hidden="true"></i>';
						break;
					default:
						$badgeMobClass = $badgeDeskClass = 'error';
						$badgeMobLabel = $badgeDeskLabel = '<i class="fa fa-times" aria-hidden="true"></i>';
				}

				$actions = '';
				if ($status != 0) {
					$actions .= '<button type="button" class="btn actions optimize-with-ai-btn" data-id="' . $id . '" data-url="' . htmlspecialchars($urlAbs) . '"><i class="fa fa-refresh" aria-hidden="true"></i></button>';
				}
				if ($status == 0) {
					$actions .= '<button type="button" class="btn actions optimize-with-ai-btn now" data-id="' . $id . '" data-url="' . htmlspecialchars($urlAbs) . '"><i class="fa fa-bolt" aria-hidden="true"></i></button>';
				}
				$actions .= '<button type="button" class="btn actions optimize-with-ai-url-copy" data-id="' . $id . '" data-url="' . htmlspecialchars($urlAbs) . '"><i class="fa fa-copy" aria-hidden="true"></i></button>';
				if (in_array((int)$status, [4, 6])) {
					$actions .= '<button type="button" class="btn actions view-page-score-now" data-url="' . htmlspecialchars($urlAbs) . '"><i class="fa fa-tachometer" aria-hidden="true"></i></button>';
				}

				$table_html .= "
					<tr>
						<td><a href=\"$urlAbs\" target=\"_blank\" rel=\"noopener noreferrer\">$short_url</a><span>$urlAbs</span></td>
						<td><span class=\"badge $badgeDeskClass\">$badgeDeskLabel</span></td>
						<td><span class=\"badge $badgeMobClass\">$badgeMobLabel</span></td>
						<td>$updated_at</td>
						<td>$actions</td>
					</tr>";
			}
		} else {
			$colspan = 5;
			$table_html .= "<tr><td colspan=\"$colspan\">No URLs found.</td></tr>";
		}

		$table_html .= '</tbody></table>';

		// Pagination HTML
		$table_html .= '<div class="pagination" data-last="' . htmlspecialchars($total_pages) . '">';
		if ($start_page > 1) {
			$table_html .= '<button type="button" class="change-p-num-oi" data-page="1">1</button>';
			if ($start_page > 2) $table_html .= '<span>...</span>';
		}
		for ($i = $start_page; $i <= $end_page; $i++) {
			$active = ($i == $page) ? 'active' : '';
			$table_html .= '<button type="button" class="change-p-num-oi ' . $active . '" data-page="' . $i . '">' . $i . '</button>';
		}
		if ($end_page < $total_pages) {
			if ($end_page < $total_pages - 1) $table_html .= '<span>...</span>';
			$table_html .= '<button type="button" class="change-p-num-oi" data-page="' . $total_pages . '">' . $total_pages . '</button>';
		}
		if ($page < $total_pages) {
			$table_html .= '<button type="button" class="change-page-next-oi" data-page="' . ($page + 1) . '">></button>';
			$table_html .= '<button type="button" class="change-page-next-last-oi" data-page="' . $total_pages . '">>></button>';
		}
		$table_html .= '</div>';

		// Stats
		$percentage = ($total_rows_all > 0 && $optimized_count > 0)
			? number_format(($optimized_count / $total_rows_all) * 100, 2)
			: 0;

		$time = '0 minutes remaining';
		$remainingPages = $total_rows_all - $optimized_count;
		if ($remainingPages > 0) {
			$pagePerBatch = !empty($this->settings['page_batch'])
				? (int)$this->settings['page_batch']
				: 1;
			$pagePerBatch = max($pagePerBatch, 1);
			$minutes = ceil($remainingPages / $pagePerBatch);
			$time = 'Estimated time: ' . $this->convertMinutesToReadableFormat($minutes) . ' remaining';
		}

		return [
			'html' => $table_html,
			'current_page' => $page,
			'total_pages' => $total_pages,
			'optimized_rows' => (int)$optimized_count,
			'total_rows' => (int)$total_rows_all,
			'percentage' => $percentage,
			'time' => $time
		];
	}

    /**
	 * Get pending optimization IDs (DB or JSON)
	 *
	 * Retrieves a list of pending optimization IDs for batch processing,
	 * using either the database or JSON file depending on the source.
	 *
	 * @param int $count Number of IDs to retrieve
	 * @return array Array of pending IDs
	 */
	function w3GetPendingIds($count) {
		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';
			$sql = "SELECT id FROM $table_name WHERE status = :status ORDER BY updated_at ASC LIMIT :limit";
			$stmt = $w3db->prepare($sql);
			// Some PDO drivers (like MySQL) require LIMIT to be bound as integer (PDO::PARAM_INT)
			$stmt->bindValue(':status', 0, \PDO::PARAM_INT);
			$stmt->bindValue(':limit', (int)$count, \PDO::PARAM_INT);
			$stmt->execute();
			$ids = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
			return $ids ?: [];
		} else {
			$jsonFilePath = W3SPEEDSTER_PATH . '/data/w3siteurls.json';

			if (!file_exists($jsonFilePath)) {
				return [];
			}

			$jsonData = file_get_contents($jsonFilePath);
			if ($jsonData === false) {
				return [];
			}

			$records = json_decode($jsonData, true);
			if (!is_array($records)) {
				return [];
			}

			$pending = array_filter($records, function($row) {
				return isset($row['status']) && $row['status'] == 0;
			});

			usort($pending, function($a, $b) {
				return strtotime($a['updated_at']) <=> strtotime($b['updated_at']);
			});

			$ids = array_column($pending, 'id');
			return array_slice($ids, 0, $count);
		}
	}

    /**
	 * Filter pending optimization IDs
	 *
	 * Filters a list of IDs to only include those that are
	 * not already optimized or in progress.
	 *
	 * @param array $ids Array of IDs to filter
	 * @return array Filtered array of pending IDs
	 */
	function w3FilterPendingOptimizationIds($ids) {
		$ids = array_filter(array_map('intval', $ids));
		if (empty($ids)) {
			return [];
		}

		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';
			$placeholders = implode(',', array_fill(0, count($ids), '?'));
			$sql = "
				SELECT id
				FROM $table_name
				WHERE status NOT IN (4, 5, 6) AND id IN ($placeholders)
			";
			$stmt = $w3db->prepare($sql);
			foreach ($ids as $idx => $id) {
				$stmt->bindValue($idx + 1, $id, \PDO::PARAM_INT);
			}
			$stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
			return $result ?: [];
		} else {
			$jsonFilePath = W3SPEEDSTER_PATH . '/data/w3siteurls.json';
			if (!file_exists($jsonFilePath)) {
				return [];
			}
			$jsonData = file_get_contents($jsonFilePath);
			if ($jsonData === false) {
				return [];
			}
			$records = json_decode($jsonData, true);
			if (!is_array($records)) {
				return [];
			}
			$idsLookup = array_flip($ids);
			$filtered = [];
			foreach ($records as $row) {
				if (
					isset($row['id'], $row['status']) &&
					isset($idsLookup[$row['id']]) &&
					!in_array($row['status'], [4, 5, 6], true)
				) {
					$filtered[] = $row['id'];
				}
			}
			return $filtered;
		}
	}

    /**
	 * Insert site URLs for optimization (DB or JSON)
	 *
	 * Populates the site URLs table or JSON file with URLs that need
	 * optimization, handling both demo and full license scenarios.
	 * Uses $w3db (PDO) if source is 'db', otherwise uses JSON file.
	 */
	public function w3InsertSiteUrls() {
		// Determine mode: DB or JSON
		$isDb = ($this->source === 'db' && $this->w3db);
		$batchSize = $_GET['batch_size'] ?? 1000;
		$table_name = 'w3_site_urls';
		$jsonFilePath = W3SPEEDSTER_PATH . '/data/w3siteurls.json';

		$this->w3TruncateSiteUrlsTable();

		// Handle demo license or not activated
		if (strpos($this->getLicenseKey(), 'w3demo') !== false || empty($this->settings['is_activated'])) {
			if ($isDb) {
				$url = '/';
				$sql = "INSERT INTO $table_name (url, status) VALUES (:url, :status)";
				$stmt = $this->w3db->prepare($sql);
				$stmt->execute([
					':url' => $url,
					':status' => 0
				]);
				$this->w3Response([
					'status' => true,
					'done' => true,
					'inserted' => 1,
					'message' => 'All URLs inserted'
				]);
			} else {
				$urlsData = [
					[
						'id' => 1,
						'url' => '/',
						'status' => 0,
						'updated_at' => date('Y-m-d H:i:s')
					]
				];
				$this->w3CreateFile($jsonFilePath, json_encode($urlsData));
				$this->w3Response(['status' => true, 'inserted' => 1]);
			}
			return;
		}

		// Get site URLs (common)
		$siteUrls = $this->getSiteUrls();
		if (empty($siteUrls) || !is_array($siteUrls)) {
			$siteUrls = $this->getSitemapUrls($this->addSettings['siteUrl']);
		}
		if (empty($siteUrls) || !is_array($siteUrls)) {
			$this->w3Response(['status' => false, 'message' => 'No URLs found']);
			return;
		}

		// Clean URLs (common)
		$cleanUrls = array_filter($siteUrls, function ($url) {
			$url = trim($url);
			return !empty($url) && strpos($url, '?') === false;
		});
		$cleanUrls = array_map(function ($url) {
			return urldecode(trim($url));
		}, $cleanUrls);

		$insertedCount = 0;

		if ($isDb) {
			// --- DB MODE ---
			$w3db = $this->w3db;
			$chunks = array_chunk($cleanUrls, $batchSize);
			foreach ($chunks as $chunk) {
				$insertRows = [];
				foreach ($chunk as $url) {
					if (empty($url) || !$this->w3IsUrlAllowedForOptimization($url)) {
						continue;
					}
					$parsedUrl = parse_url($url);
					if (empty($parsedUrl['host']) || empty($parsedUrl['scheme'])) continue;
					$url = $this->w3ChangeUrlAbsoluteToRelative($url, $parsedUrl);
					$url = empty($url) ? '/' : $url;

					// Check if URL already exists
					$sqlExists = "SELECT COUNT(*) FROM $table_name WHERE url = :url";
					$stmtExists = $w3db->prepare($sqlExists);
					$stmtExists->execute([':url' => $url]);
					$exists = $stmtExists->fetchColumn();

					if ($exists) {
						continue;
					}

					$insertRows[] = [
						'url' => $url,
						'status' => 0
					];
					$insertedCount++;
				}

				// Batch insert
				if (!empty($insertRows)) {
					$values = [];
					$params = [];
					foreach ($insertRows as $idx => $row) {
						$values[] = "(:url$idx, :status$idx)";
						$params[":url$idx"] = $row['url'];
						$params[":status$idx"] = $row['status'];
					}
					$insertQuery = "INSERT INTO $table_name (url, status) VALUES " . implode(',', $values);
					$stmtInsert = $w3db->prepare($insertQuery);
					$stmtInsert->execute($params);
				}
			}

			$this->w3Response([
				'status' => true,
				'done' => true,
				'inserted' => $insertedCount,
				'message' => 'All URLs inserted in chunks of ' . $batchSize
			]);
		} else {
			// --- JSON MODE ---
			$allData = [];
			$idCounter = 1;
			foreach ($cleanUrls as $url) {
				if (empty($url) || !$this->w3IsUrlAllowedForOptimization($url)) {
					continue;
				}
				$parsedUrl = parse_url($url);
				if (empty($parsedUrl['host']) || empty($parsedUrl['scheme'])) continue;

				$url = $this->w3ChangeUrlAbsoluteToRelative($url, $parsedUrl);
				$url = empty($url) ? '/' : $url;

				$allData[] = [
					'id' => $idCounter++,
					'url' => $url,
					'status' => 0,
					'updated_at' => date('Y-m-d H:i:s')
				];
				++$insertedCount;
			}
			$this->w3CreateFile($jsonFilePath, json_encode($allData));
			$this->w3Response(['status' => true, 'inserted' => $insertedCount]);
		}
	}

    /**
	 * Reset optimization status
	 *
	 * Resets all optimization statuses to pending (0) in the database
	 * or JSON file, depending on the source.
	 */
	public function w3ResetOptimizationStatus() {
		// combine those function like 
		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';
			$query = "UPDATE $table_name SET status = :status, updated_at = :updated_at";
			$stmt = $w3db->prepare($query);
			$stmt->execute([
				':status' => 0,
				':updated_at' => date('Y-m-d H:i:s')
			]);
			return true;
		} else {
			$jsonFilePath = W3SPEEDSTER_PATH . '/data/w3siteurls.json';
			if (!file_exists($jsonFilePath)) {
				return false;
			}

			$data = json_decode(file_get_contents($jsonFilePath), true);

			if (!is_array($data)) {
				return false;
			}

			foreach ($data as &$row) {
				$row['status'] = 0;
				$row['updated_at'] = date('Y-m-d H:i:s');
			}

			$saved = $this->w3speedsterPutContents($jsonFilePath, json_encode($data));
			return $saved !== false;
		}
	}


    /**
	 * Insert page if not exists
	 *
	 * Adds the current page to the optimization queue if it
	 * doesn't already exist in the database.
	 *
	 * @return bool True if inserted, false otherwise
	 */
	function w3InsertPageIfNotExists() {
		$url = $this->addSettings['fullUrlWithoutParam'];
		if (empty($url) || !$this->w3IsUrlAllowedForOptimization($url)) {
			return false;
		}
		$parsedUrl = parse_url($url);
		if(empty($parsedUrl['host']) || empty($parsedUrl['scheme'])) return false;
		$url = $this->w3ChangeUrlAbsoluteToRelative($url, $parsedUrl);

		$w3db = $this->w3db;
		$table_name = 'w3_site_urls';

		// Check if the URL already exists
		$query = "SELECT id FROM $table_name WHERE url = :url LIMIT 1";
		$stmt = $w3db->prepare($query);
		$stmt->execute([':url' => $url]);
		$exists = $stmt->fetchColumn();

		if ($exists) {
			return false;
		}

		// Insert the new URL
		$insertQuery = "INSERT INTO $table_name (url, status) VALUES (:url, :status)";
		$insertStmt = $w3db->prepare($insertQuery);
		$inserted = $insertStmt->execute([
			':url' => $url,
			':status' => 0
		]);

		return $inserted;
	}

    /**
	 * Update site URLs status
	 *
	 * Updates the status of multiple site URLs in the database or JSON file
	 * for tracking optimization progress.
	 *
	 * @param array $ids    Array of IDs to update
	 * @param int   $status New status value
	 * @return int|false Number of affected rows or true/false for JSON, false on failure
	 */
	public function w3UpdateSiteUrlsStatus($ids, $status = 0) {
		if (empty($ids)) {
			return false;
		}

		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';
			$placeholders = implode(',', array_fill(0, count($ids), '?'));
			$query = "
				UPDATE $table_name 
				SET status = ?, updated_at = ? 
				WHERE id IN ($placeholders)
			";

			$params = array_merge(
				[$status, date('Y-m-d H:i:s')],
				$ids
			);
			$stmt = $w3db->prepare($query);
			$result = $stmt->execute($params);
			if ($result !== false) {
				return $stmt->rowCount();
			}
			return false;
		} else {
			$jsonFile = W3SPEEDSTER_PATH . '/data/w3siteurls.json';

			if (!file_exists($jsonFile)) {
				return false;
			}

			$jsonContent = file_get_contents($jsonFile);
			$data = json_decode($jsonContent, true);

			if (!is_array($data)) {
				return false;
			}

			$updated = false;
			$currentTime = date("Y-m-d H:i:s");

			foreach ($data as &$item) {
				if (in_array($item['id'], $ids)) {
					$item['status'] = $status;
					$item['updated_at'] = $currentTime;
					$updated = true;
				}
			}

			if ($updated) {
				$this->w3speedsterPutContents($jsonFile, json_encode($data));
				return true;
			}
			return false;
		}
	}

    /**
     * Run background cron tasks (DB or JSON source)
     *
     * Executes background optimization tasks including crawling
     * and optimization of URLs in batches, using either DB or JSON file as source.
     */
    public function w3RunBackgroundCron($task, $ids, $request_count) {
        // Determine source: DB or JSON
        $useDb = ($this->source === 'db' && $this->w3db);
		if (empty($task)) return;
		$ids = (empty($ids) && !is_array($ids)) ? [] : $ids;

        if ($useDb) {
            $w3db = $this->w3db;
            $table_name = 'w3_site_urls';
        } else {
            $jsonFile = W3SPEEDSTER_PATH . '/data/w3siteurls.json';
            $rows = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
        }

        if ($task === 'crawl') {
            $data = $this->w3GetUrlsByIds($ids);

            if ($useDb) {
                $this->w3UpdateSiteUrlsStatus($ids, 1);
            } else {
                $currentTime = date('Y-m-d H:i:s');
                foreach ($rows as &$row) {
                    if (in_array($row['id'], $ids)) {
                        $row['status'] = 1;
                        $row['updated_at'] = $currentTime;
                    }
                }
                $this->w3CreateFile($jsonFile, json_encode($rows));
            }

            foreach ($data as $item) {
                $url = trim($item['url']);
				$token = md5(rtrim($item['url'], '/'));
				$cssFilesCount = $this->w3CssFilesCount($url);
				$criticalTokenMobile = $this->addSettings['criticalCssPath'] . '/tokens/' . $token . '-mobile';
				$criticalTokenDesktop = $this->addSettings['criticalCssPath'] . '/tokens/' . $token . '-desktop';
				[$mobCacheFile, $desktopCacheFile] = $this->w3speedsterCacheFilePath($url);
				if ($cssFilesCount['mobile'] < 1 && !is_file($criticalTokenMobile)) {
					if(file_exists($mobCacheFile)){
						$this->w3DeleteFile($mobCacheFile);
					}
					$this->w3RemoteGet($url, [], true);
				}
				if ($cssFilesCount['desktop'] < 1 && !is_file($criticalTokenDesktop)) {
					if(file_exists($desktopCacheFile)){
						$this->w3DeleteFile($desktopCacheFile);
					}
					$this->w3RemoteGet($url, []);
				}
            }
        } else if ($task === 'optimize') {
            $data = $this->w3GetUrlsByIds($ids);

            foreach ($data as $item) {
                if ($item['status'] == 4) continue;
				$id = $item['id'];
				$url = rtrim($item['url'], '/');
				$currentStatus = $item['status'];
				$token = md5($url);
				$webpToken = $this->addSettings['webp_path'] . '/' . $token;
				$cssFilesCount = $this->w3CssFilesCount($url);
				$criticalTokenMobile = $this->addSettings['criticalCssPath'] . '/tokens/' . $token . '-mobile';
				$criticalTokenDesktop = $this->addSettings['criticalCssPath'] . '/tokens/' . $token . '-desktop';
				$mobile = false;
				$desktop = false;
				$status = 1;
				if ($cssFilesCount['mobile'] > 0) {
					$mobile = true;
				}
				if ($cssFilesCount['desktop'] > 0) {
					$desktop = true;
				}
				if ($desktop && !$mobile) {
					$status = 2;
				} else if (!$desktop && $mobile) {
					$status = 3;
				} else if ($desktop && $mobile) {
					$status = 4;
				}
				if ($request_count >= 12 && $status != 4) {
					$status = 5;
				}

				if ($currentStatus != $status) {
					if ($useDb) {
						$query = "UPDATE $table_name SET status = :status, updated_at = :updated_at WHERE id = :id";
						$stmt = $w3db->prepare($query);
						$stmt->execute([
							':status' => $status,
							':updated_at' => date('Y-m-d H:i:s'),
							':id' => $id
						]);
					} else {
						foreach ($rows as &$row) {
							if ($row['id'] == $id) {
								$row['status'] = $status;
								$row['updated_at'] = date('Y-m-d H:i:s');
								break;
							}
						}
						unset($row);
						$this->w3CreateFile($jsonFile, json_encode($rows));
					}
					$currentStatus = $status;
				}

                if (in_array($currentStatus, [1, 2, 3])) {
					[$mobCacheFile, $desktopCacheFile] = $this->w3speedsterCacheFilePath($url);
					if ($currentStatus != 3 && $cssFilesCount['mobile'] < 1 && !is_file($criticalTokenMobile)) {
						if (file_exists($mobCacheFile)) {
							$this->w3DeleteFile($mobCacheFile);
						}
						$this->w3RemoteGet($url, [], true);
					} 
					if ($currentStatus != 2 && $cssFilesCount['desktop'] < 1 && !is_file($criticalTokenDesktop)) {
						if (file_exists($desktopCacheFile)) {
							$this->w3DeleteFile($desktopCacheFile);
						}
						$this->w3RemoteGet($url, []);
					}
				}
				if (file_exists($webpToken) || file_exists($criticalTokenMobile) || file_exists($criticalTokenDesktop)) {
					$this->w3SaveDataWithAjax($token, false, true);
				} else {
					continue;
				}
				$updateStatus = !file_exists($webpToken) || !file_exists($criticalTokenMobile) || !file_exists($criticalTokenDesktop);
				if ($updateStatus) {
					[$mobCacheFile, $desktopCacheFile] = $this->w3speedsterCacheFilePath($url);
					if (file_exists($mobCacheFile)) {
						$this->w3DeleteFile($mobCacheFile);
					}
					if (file_exists($desktopCacheFile)) {
						$this->w3DeleteFile($desktopCacheFile);
					}
				}
            }

        }
    }

    /**
	 * Get URLs by IDs
	 *
	 * Retrieves URL data from the database or JSON file based on an array of IDs
	 * and converts relative URLs to absolute URLs.
	 *
	 * @param array $ids Array of IDs to retrieve
	 * @return array Array of URL data
	 */
	public function w3GetUrlsByIds(array $ids) {
		if (empty($ids)) {
			return [];
		}

		// Prefer DB if source is 'db' and $w3db is set, otherwise use JSON file
		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';
			$placeholders = implode(',', array_fill(0, count($ids), '?'));
			$query = "SELECT id, url, status FROM $table_name WHERE id IN ($placeholders)";
			$stmt = $w3db->prepare($query);
			$stmt->execute($ids);
			$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

			return array_map(function($item){
				$item['url'] = $this->w3ChangeUrlRelativeToAbsolute($item['url']); 
				return $item;
			}, $data);
		} else {
			$jsonFile = W3SPEEDSTER_PATH . '/data/w3siteurls.json';

			if (!file_exists($jsonFile)) {
				return [];
			}

			$jsonContent = file_get_contents($jsonFile);
			$data = json_decode($jsonContent, true);

			if (!is_array($data)) {
				return [];
			}

			$filtered = array_filter($data, function($item) use ($ids) {
				return in_array($item['id'], $ids);
			});

			$result = array_map(function($item) {
				$item['url'] = $this->w3ChangeUrlRelativeToAbsolute($item['url']);
				return $item;
			}, $filtered);

			return array_values($result);
		}
	}

    /**
	 * Truncate site URLs table
	 *
	 * Clears all data from the site URLs table to allow
	 * fresh population with current site URLs.
	 */
	function w3TruncateSiteUrlsTable() {
		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';
			// Disable foreign key checks, truncate, then re-enable
			$w3db->exec('SET FOREIGN_KEY_CHECKS = 0');
			$w3db->exec("TRUNCATE TABLE $table_name");
			$w3db->exec('SET FOREIGN_KEY_CHECKS = 1');
		} else {
			$jsonFile = W3SPEEDSTER_PATH . '/data/w3siteurls.json';
			if (file_exists($jsonFile)) {
				$this->w3speedsterPutContents($jsonFile, json_encode([]));
			}
		}
	}

    /**
	 * Remove no-optimize page if exists
	 *
	 * Removes the current page from the optimization queue
	 * if it exists and should not be optimized.
	 *
	 * @return bool True if removed, false otherwise
	 */
	public function w3RemoveNoOptimizePageIfExists() {
		$url = trim($this->addSettings['fullUrlWithoutParam']);
		if (empty($url)) {
			return false;
		}
		$parsedUrl = parse_url($url);
		if (empty($parsedUrl['host']) || empty($parsedUrl['scheme'])) {
			return false;
		}
		$url = $this->w3ChangeUrlAbsoluteToRelative($url, $parsedUrl);

		// Combine DB and JSON logic
		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';

			// Check if the URL exists
			$stmt = $w3db->prepare("SELECT id FROM $table_name WHERE url = ? LIMIT 1");
			$stmt->execute([$url]);
			$exists = $stmt->fetchColumn();

			if (!$exists) {
				return false;
			}

			// Delete the row
			$stmt = $w3db->prepare("DELETE FROM $table_name WHERE url = ?");
			$deleted = $stmt->execute([$url]);

			return $deleted !== false;
		} else {
			$jsonFilePath = W3SPEEDSTER_PATH . '/data/w3siteurls.json';

			if (!file_exists($jsonFilePath)) {
				return false;
			}

			$jsonData = file_get_contents($jsonFilePath);
			$data = json_decode($jsonData, true);

			if (!is_array($data)) {
				$data = [];
			}

			$foundIndex = null;
			foreach ($data as $index => $row) {
				if (isset($row['url']) && $row['url'] === $url) {
					$foundIndex = $index;
					break;
				}
			}

			if ($foundIndex === null) {
				return false;
			}

			unset($data[$foundIndex]);
			$data = array_values($data);
			$this->w3CreateFile($jsonFilePath, json_encode($data));
			return true;
		}
	}


    /**
	 * Reset single page optimization
	 *
	 * Resets the optimization status of a single page to allow
	 * re-optimization and clears associated cache files.
	 *
	 * @return bool True if reset successful, false otherwise
	 */
	public function w3ResetSinglePageOptimiation() {
		$id = $_REQUEST['id'] ?? null;
		if (empty($id) || !is_numeric($id)) {
			return false;
		}

		if ($this->source === 'db' && $this->w3db) {
			$w3db = $this->w3db;
			$table_name = 'w3_site_urls';
			$stmt = $w3db->prepare("SELECT id, url FROM $table_name WHERE id = ? LIMIT 1");
			$stmt->execute([$id]);
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);

			if (empty($row)) {
				return false;
			}

			$url = $this->w3ChangeUrlRelativeToAbsolute($row['url']);

			[$mobCacheFile, $desktopCacheFile] = $this->w3speedsterCacheFilePath($url);

			if (file_exists($mobCacheFile)) {
				$this->w3DeleteFile($mobCacheFile);
			}
			if (file_exists($desktopCacheFile)) {
				$this->w3DeleteFile($desktopCacheFile);
			}

			// Update the status to 0
			$stmt = $w3db->prepare("UPDATE $table_name SET status = 0 WHERE id = ?");
			$updated = $stmt->execute([$id]);

			return $updated !== false;
		} else {
			$jsonFile = W3SPEEDSTER_PATH . '/data/w3siteurls.json';

			if (!file_exists($jsonFile)) {
				return false;
			}

			$jsonContent = file_get_contents($jsonFile);
			$data = json_decode($jsonContent, true);

			if (!is_array($data)) {
				return false;
			}

			$row = null;
			foreach ($data as $item) {
				if ($item['id'] == $id) {
					$row = $item;
					break;
				}
			}

			if (empty($row)) {
				return false;
			}

			$url = $this->w3ChangeUrlRelativeToAbsolute($row['url']);

			[$mobCacheFile, $desktopCacheFile] = $this->w3speedsterCacheFilePath($url);

			if (file_exists($mobCacheFile)) {
				$this->w3DeleteFile($mobCacheFile);
			}
			if (file_exists($desktopCacheFile)) {
				$this->w3DeleteFile($desktopCacheFile);
			}

			$updated = false;
			foreach ($data as &$item) {
				if ($item['id'] == $id) {
					$item['status'] = 0;
					$item['updated_at'] = date("Y-m-d H:i:s");
					$updated = true;
					break;
				}
			}

			if ($updated) {
				$this->w3speedsterPutContents($jsonFile, json_encode($data));
				return true;
			}

			return false;
		}
	}

    /**
	 * Get option
	 *
	 * Retrieves plugin options from either the database (w3_options table)
	 * or from a file, depending on the source and w3db connection.
	 *
	 * @param string $option   Option name.
	 * @param bool   $is_array Whether the option is an array.
	 * @return mixed Option value
	 */
	function w3GetOption($option, $is_array = 1)
	{
		// If using DB and w3db is set, fetch from w3_options table
		if ($this->source === 'db' && $this->w3db) {
			try {
				$sql = "SELECT option_value FROM w3_options WHERE option_name = :option_name LIMIT 1";
				$stmt = $this->w3db->prepare($sql);
				$stmt->bindValue(':option_name', $option, \PDO::PARAM_STR);
				$stmt->execute();
				$row = $stmt->fetch(\PDO::FETCH_ASSOC);
				if ($row && isset($row['option_value'])) {
					if ($is_array) {
						$value = @unserialize($row['option_value']);
						return is_array($value) ? $value : array();
					} else {
						return $row['option_value'];
					}
				} else {
					return $is_array ? array() : '';
				}
			} catch (\Exception $e) {
				// Fallback to file if DB fails
			}
		}

		// Fallback to file-based option
		$filePath = W3SPEEDSTER_PATH . '/data/' .  $option . '.php';
		$folderPath = W3SPEEDSTER_PATH . '/data/';
		if (!is_dir($folderPath)) {
			mkdir($folderPath, 0777, true);
		}
		if (is_file($filePath)) {
			if ($is_array) {
				$value = @unserialize(file_get_contents($filePath));
				return is_array($value) ? $value : array();
			} else {
				return file_get_contents($filePath);
			}
		}
		return $is_array ? array() : '';
	}

    
	/**
	 * Update option
	 *
	 * Updates plugin options in the database (w3_options table) if using DB,
	 * otherwise falls back to file-based storage.
	 *
	 * @param string $option   Option name.
	 * @param mixed  $value    Option value.
	 * @param string $autoload Autoload value (unused, for compatibility).
	 * @param int    $array    Whether the value is an array (1) or not (0).
	 * @return int|bool 1 on success, 0 or false on failure
	 */
	function w3UpdateOption($option, $value, $autoload = null, $array = 1)
	{
		// If using DB and w3db is set, update in w3_options table
		if ($this->source === 'db' && $this->w3db) {
			try {
				$option_value = $array ? serialize($value) : $value;
				// Try to update first
				$sql = "UPDATE w3_options SET option_value = :option_value WHERE option_name = :option_name";
				$stmt = $this->w3db->prepare($sql);
				$stmt->bindValue(':option_value', $option_value, \PDO::PARAM_STR);
				$stmt->bindValue(':option_name', $option, \PDO::PARAM_STR);
				$stmt->execute();
				if ($stmt->rowCount() === 0) {
					// If not updated, insert
					$sql = "INSERT INTO w3_options (option_name, option_value) VALUES (:option_name, :option_value)";
					$stmt = $this->w3db->prepare($sql);
					$stmt->bindValue(':option_name', $option, \PDO::PARAM_STR);
					$stmt->bindValue(':option_value', $option_value, \PDO::PARAM_STR);
					$stmt->execute();
				}
				return 1;
			} catch (\Exception $e) {
				// Fallback to file if DB fails
			}
		}

		// Fallback to file-based option
		$path = W3SPEEDSTER_PATH . '/data/' .  $option . '.php';
		$folderPath = W3SPEEDSTER_PATH . '/data/';
		if (!is_dir($folderPath)) {
			mkdir($folderPath, 0777, true);
		}
		$file = fopen($path, 'w');
		$res = fwrite($file, ($array ? serialize($value) : $value));
		fclose($file);
		return $res;
	}

    /**
	 * Delete old settings change logs
	 *
	 * Removes settings change log entries older than 3 months
	 * to maintain database performance.
	 *
	 * @return bool True on success
	 */
	function deleteSettingsChangeLog() {
		if ($this->source === 'db' && $this->w3db) {
			try {
				$sql = "DELETE FROM w3_change_logs WHERE time < DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
				$this->w3db->exec($sql);
				return true;
			} catch (\Exception $e) {
				// If DB operation fails, fall back to file-based cleanup
			}
		}

		// Fallback to file-based cleanup (JSON log)
		$jsonFilePath = W3SPEEDSTER_PATH . '/data/changeLogs.json';
		if (!file_exists($jsonFilePath)) {
			return true;
		}
		$changeLogs = json_decode(file_get_contents($jsonFilePath), true) ?? [];
		$now = time();
		$cutoffTimestamp = strtotime('-3 months', $now);
		$changeLogs = array_filter($changeLogs, function ($entry) use ($cutoffTimestamp) {
			return strtotime($entry['time']) >= $cutoffTimestamp;
		});
		$this->w3speedsterPutContents($jsonFilePath, json_encode($changeLogs));
		return true;
	}

    /**
	 * Delete old Core Web Vitals logs
	 *
	 * Removes Core Web Vitals log entries older than 3 months
	 * to maintain database performance.
	 *
	 * @return bool True on success
	 */
	function deleteCoreWebVitalsLog(){
		if ($this->source === 'db' && $this->w3db) {
			try {
				$sql = "DELETE FROM w3_core_webvitals WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
				$this->w3db->exec($sql);
				return true;
			} catch (\Exception $e) {
				// If DB operation fails, fall back to file-based cleanup
			}
		}

		// Fallback to file-based cleanup (JSON log)
		$jsonFilePath = W3SPEEDSTER_PATH . '/data/webvitals.json';
		if (!file_exists($jsonFilePath)) {
			return true;
		}
		$coreWebVitalsLogs = json_decode(file_get_contents($jsonFilePath), true) ?? [];
		$now = time();
		$cutoffTimestamp = strtotime('-3 months', $now);
		$coreWebVitalsLogs = array_filter($coreWebVitalsLogs, function ($entry) use ($cutoffTimestamp) {
			return isset($entry['timestamp']) && strtotime($entry['timestamp']) >= $cutoffTimestamp;
		});
		$this->w3speedsterPutContents($jsonFilePath, json_encode($coreWebVitalsLogs));
		return true;
	}

	public function insertWebVitals() {
		$url        = isset($_POST['url']) ? trim($_POST['url']) : '';
		$issueType  = isset($_POST['issueType']) ? trim($_POST['issueType']) : '';
		$data       = isset($_POST['data']) ? trim($_POST['data']) : '';
		$deviceType = isset($_POST['deviceType']) ? trim($_POST['deviceType']) : '';
		$timestamp  = date('Y-m-d H:i:s');

		if ($this->source === 'db' && $this->w3db) {
			try {
				$sql = "INSERT INTO w3_core_webvitals (url, issuetype, data, deviceType, timestamp) VALUES (:url, :issuetype, :data, :deviceType, :timestamp)";
				$stmt = $this->w3db->prepare($sql);
				$result = $stmt->execute([
					':url'        => $url,
					':issuetype'  => $issueType,
					':data'       => $data,
					':deviceType' => $deviceType,
					':timestamp'  => $timestamp
				]);
				echo $result ? 1 : 0;
			} catch (\Exception $e) {
				echo 0;
			}
			exit;
		}

		// Fallback to file-based storage (JSON)
		$jsonFilePath = W3SPEEDSTER_PATH . '/data/webvitals.json';
		$logs = [];
		if (file_exists($jsonFilePath)) {
			$logs = json_decode(file_get_contents($jsonFilePath), true) ?? [];
		}
		// Generate next ID
		$nextId = 1;
		if (!empty($logs)) {
			$ids = array_column($logs, 'id');
			$nextId = max($ids) + 1;
		}
		$logs[] = [
			'id'         => $nextId,
			'url'        => $url,
			'issuetype'  => $issueType,
			'data'       => $data,
			'deviceType' => $deviceType,
			'timestamp'  => $timestamp
		];
		$saved = $this->w3speedsterPutContents($jsonFilePath, json_encode($logs));
		echo $saved !== false ? 1 : 0;
		exit;
	}

	/**
	 * Handle delete log data
	 */
	public function deleteWebVitals() {
		// Accept time_interval from POST (sanitize as needed)
		$time_interval = isset($_POST['time_interval']) ? trim($_POST['time_interval']) : '';

		// Map intervals to SQL and PHP strtotime
		$intervals = [
			'last7days'   => ['sql' => '7 DAY',    'php' => '-7 days'],
			'lastMonth'   => ['sql' => '30 DAY',   'php' => '-30 days'],
			'last3months' => ['sql' => '3 MONTH',  'php' => '-3 months'],
			'last6months' => ['sql' => '6 MONTH',  'php' => '-6 months'],
			'lastYear'    => ['sql' => '1 YEAR',   'php' => '-1 year'],
			'all'         => ['sql' => '',         'php' => 'all']
		];

		if (!isset($intervals[$time_interval])) {
			echo json_encode(['status' => false, 'message' => 'Invalid time interval']);
			exit;
		}

		if ($this->source === 'db' && $this->w3db) {
			$table_name = 'w3_core_webvitals';
			try {
				if ($time_interval === 'all') {
					$sql = "DELETE FROM $table_name";
				} else {
					$sql = "DELETE FROM $table_name WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL {$intervals[$time_interval]['sql']})";
				}
				$result = $this->w3db->exec($sql);
				echo $this->w3SpeedsterGetLogData();
			} catch (\Exception $e) {
				echo json_encode(['status' => false, 'message' => 'Error deleting data']);
			}
			exit;
		}

		// --- File Mode ---
		$jsonFilePath = W3SPEEDSTER_PATH . '/data/webvitals.json';
		if (!file_exists($jsonFilePath)) {
			echo json_encode(['status' => true, 'deleted' => 0, 'logData' => $this->w3SpeedsterGetLogData()]);
			exit;
		}
		$logs = json_decode(file_get_contents($jsonFilePath), true) ?? [];
		$deleted = 0;

		if ($time_interval === 'all') {
			$deleted = count($logs);
			$logs = [];
		} else {
			$cutoff = strtotime($intervals[$time_interval]['php']);
			$logs = array_filter($logs, function ($entry) use ($cutoff, &$deleted) {
				if (!isset($entry['timestamp'])) return false;
				$keep = strtotime($entry['timestamp']) >= $cutoff;
				if (!$keep) $deleted++;
				return $keep;
			});
		}
		$this->w3speedsterPutContents($jsonFilePath, json_encode(array_values($logs)));
		echo $this->w3SpeedsterGetLogData();
		exit;
	}


	/**
	 * Handle show URL suggestions
	 */
	public function w3SpeedsterShowUrlSuggestions() {
		$search_term = isset($_POST['s_text']) ? trim($_POST['s_text']) : '';
		$url_array = [];

		if ($this->source === 'db' && $this->w3db) {
			$table_name = 'w3_core_webvitals';
			try {
				$sql = "SELECT url FROM $table_name WHERE url LIKE :search_query GROUP BY url LIMIT 10";
				$stmt = $this->w3db->prepare($sql);
				$stmt->bindValue(':search_query', '%' . $search_term . '%', \PDO::PARAM_STR);
				$stmt->execute();
				$results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
				foreach ($results as $row) {
					$url_array[] = $row['url'];
				}
			} catch (\Exception $e) {
				// On error, return empty array
			}
		} else {
			// --- File Mode ---
			$jsonFilePath = W3SPEEDSTER_PATH . '/data/webvitals.json';
			if (file_exists($jsonFilePath)) {
				$logs = json_decode(file_get_contents($jsonFilePath), true) ?? [];
				$found = [];
				foreach ($logs as $entry) {
					if (!empty($entry['url']) && stripos($entry['url'], $search_term) !== false) {
						$found[$entry['url']] = true;
						if (count($found) >= 10) break;
					}
				}
				$url_array = array_keys($found);
			}
		}

		echo json_encode($url_array);
		exit;
	}

	/**
	 * Delete settings change log data (DB or JSON)
	 *
	 * Deletes settings change log entries from the database or JSON file
	 * based on the selected time interval.
	 */
	public function w3SpeedsterDeleteChangeLogData() {
		// Accept time_interval from POST (sanitize as needed)
		$time_interval = isset($_POST['time_interval']) ? trim($_POST['time_interval']) : '';

		// Map intervals to SQL and PHP strtotime
		$intervals = [
			'last7days'   => ['sql' => '7 DAY',    'php' => '-7 days'],
			'lastMonth'   => ['sql' => '30 DAY',   'php' => '-30 days'],
			'last3months' => ['sql' => '3 MONTH',  'php' => '-3 months'],
			'last6months' => ['sql' => '6 MONTH',  'php' => '-6 months'],
			'lastYear'    => ['sql' => '1 YEAR',   'php' => '-1 year'],
			'all'         => ['sql' => '',         'php' => 'all']
		];

		if (!isset($intervals[$time_interval])) {
			echo json_encode(['status' => false, 'message' => 'Invalid time interval']);
			exit;
		}

		if ($this->source === 'db' && $this->w3db) {
			$table_name = 'w3_change_logs';
			try {
				if ($time_interval === 'all') {
					$sql = "DELETE FROM $table_name";
				} else {
					$sql = "DELETE FROM $table_name WHERE time < DATE_SUB(CURDATE(), INTERVAL {$intervals[$time_interval]['sql']})";
				}
				$result = $this->w3db->exec($sql);
				echo $this->w3SpeedsterGetChangeLogData();
			} catch (\Exception $e) {
				echo '';
			}
			exit;
		}

		// --- File Mode ---
		$jsonFilePath = W3SPEEDSTER_PATH . '/data/changeLogs.json';
		if (!file_exists($jsonFilePath)) {
			echo json_encode(['status' => true, 'deleted' => 0, 'logData' => $this->w3SpeedsterGetChangeLogData()]);
			exit;
		}
		$logs = json_decode(file_get_contents($jsonFilePath), true) ?? [];
		$deleted = 0;

		if ($time_interval === 'all') {
			$deleted = count($logs);
			$logs = [];
		} else {
			$cutoff = strtotime($intervals[$time_interval]['php']);
			$logs = array_filter($logs, function ($entry) use ($cutoff, &$deleted) {
				if (!isset($entry['time'])) return false;
				$keep = strtotime($entry['time']) >= $cutoff;
				if (!$keep) $deleted++;
				return $keep;
			});
		}
		$this->w3speedsterPutContents($jsonFilePath, json_encode(array_values($logs)));
		echo $this->w3SpeedsterGetChangeLogData();
		exit;
	}

	public function w3SpeedsterHandleExportSettings() {
		$settings = $this->settings;
		$settings = is_array($settings) ? $settings : array();
		$payload = serialize( $settings );
		$filename = $this->addSettings['siteUrlArr']['host'] . '-' . gmdate('Y-m-d-H-i-s') . '.dat';
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Content-Length: ' . strlen($payload));
		echo $payload;
		exit;
	}

	public function w3SpeedsterImportSettings() {
		$raw = file_get_contents( $_FILES['w3_import_file']['tmp_name'] );
		$raw = is_string($raw) ? trim($raw) : '';
		$data = @unserialize( $raw );
		if ( $data === false || ! is_array( $data ) ) {
			$decoded = base64_decode( $raw, true );
			if ( is_string($decoded) ) {
				$data = @unserialize( $decoded );
			}
		}
		if ( is_array( $data ) ) {
			$this->w3UpdateOption( 'w3_speedup_option', $data, 'no' );
			$this->pageReload();
		}
	}
}
