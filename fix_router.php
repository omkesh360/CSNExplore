<?php
$content = file_get_contents("router.php");
$correct = <<<EOD
<?php
// router.php - Handles clean URLs using nikic/fast-route

if (file_exists(__DIR__ . "/php/config.php")) {
    require_once __DIR__ . "/php/config.php";
}

if (defined("APP_ENV") && APP_ENV === "local") {
    error_reporting(E_ALL);
    ini_set("display_errors", "1");
} else {
    error_reporting(0);
    ini_set("display_errors", "0");
}

// -- 301 redirects (legacy URLs) -----------------------------------------------
if (file_exists("php/redirects.php")) {
    require_once "php/redirects.php";
}

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector \$r) {
    // Static Routes
    \$r->addRoute("GET", "/", "index.php");
    \$r->addRoute("GET", "/index", "index.php");
    \$r->addRoute("GET", "/index.php", "index.php");
    \$r->addRoute("GET", "/about", "about.php");
    \$r->addRoute(["GET", "POST"], "/contact", "contact.php");
    \$r->addRoute("GET", "/blogs", "blogs.php");
    \$r->addRoute(["GET", "POST"], "/login", "login.php");
    \$r->addRoute(["GET", "POST"], "/register", "register.php");
    \$r->addRoute("GET", "/privacy", "privacy.php");
    \$r->addRoute("GET", "/terms", "terms.php");
    \$r->addRoute("GET", "/my-booking", "my-booking.php");
    \$r->addRoute("GET", "/bus", "bus.php");
    \$r->addRoute("GET", "/blog-detail", "blog-detail.php");
    \$r->addRoute(["GET", "POST"], "/subscribe", "subscribe.php");
    \$r->addRoute("GET", "/install", "install.php");
    \$r->addRoute("GET", "/faq", "faq.php");
    \$r->addRoute("GET", "/itineraries", "itineraries.php");
    \$r->addRoute("GET", "/near-attractions", "near-attractions.php");
    \$r->addRoute("GET", "/compare", "compare.php");
    \$r->addRoute("GET", "/suggestor", "suggestor.php");
    \$r->addRoute("GET", "/travel-guide", "travel-guide.php");
    \$r->addRoute("GET", "/explore", "explore.php"); // ADDED EXPLICIT ROUTE

    // Dynamic Routes
    \$r->addRoute("GET", "/listing/{type}[/]", "listing.php");
    \$r->addRoute("GET", "/listing", "listing.php");
    
    // Legacy URLs support
    \$r->addRoute("GET", "/listing-detail/{slug}[/]", "listing-detail-handler");
    \$r->addRoute("GET", "/blogs/{slug}[/]", "blogs-handler");
});

// Fetch method and URI
\$httpMethod = \$_SERVER["REQUEST_METHOD"] ?? "GET";
\$uri = \$_SERVER["REQUEST_URI"] ?? "/";

if (false !== \$pos = strpos(\$uri, "?")) {
    \$uri = substr(\$uri, 0, \$pos);
}
// For local CLI server compat if subfolder is not used, else normal
\$base = rtrim(dirname(\$_SERVER["SCRIPT_NAME"] ?? ""), "/");
if (\$base !== "" && strpos(\$uri, \$base) === 0) {
    \$uri = substr(\$uri, strlen(\$base));
}
\$uri = rtrim(rawurldecode(\$uri), "/");
if (\$uri === "") \$uri = "/";

\$path = ltrim(\$uri, "/");

// -- 1. Real files (images, css, js, etc.) ------------------------------------
if (\$path !== "" && file_exists(__DIR__ . "/" . \$path) && !is_dir(__DIR__ . "/" . \$path)) {
    if (php_sapi_name() === "cli-server") {
        return false;
    } else {
        if (str_ends_with(\$path, ".html")) {
            header("Content-Type: text/html; charset=UTF-8");
        }
        include __DIR__ . "/" . \$path;
        return;
    }
}

// Redirect /index ? / (canonical URL)
if (\$uri === "/index") {
    header("Location: " . \$base . "/", true, 301);
    exit;
}

\$routeInfo = \$dispatcher->dispatch(\$httpMethod, \$uri);
switch (\$routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // Try direct file fallback or 404
        if (\$path !== "" && file_exists(__DIR__ . "/" . \$path . ".php")) {
            include __DIR__ . "/" . \$path . ".php";
        } elseif (\$path !== "" && file_exists(__DIR__ . "/" . \$path . ".html")) {
            header("Content-Type: text/html; charset=UTF-8");
            include __DIR__ . "/" . \$path . ".html";
        } elseif (is_dir(__DIR__ . "/" . \$path) && file_exists(__DIR__ . "/" . \$path . "/index.php")) {
            include __DIR__ . "/" . \$path . "/index.php";
        } else {
            http_response_code(404);
            include "404.php";
        }
        break;
        
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        \$allowedMethods = \$routeInfo[1];
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;
        
    case FastRoute\Dispatcher::FOUND:
        \$handler = \$routeInfo[1];
        \$vars = \$routeInfo[2];
        
        // Populate \$_GET for compatibility with existing code
        if (!empty(\$vars)) {
            foreach (\$vars as \$key => \$val) {
                \$_GET[\$key] = \$val;
            }
        }
        
        if (\$handler === "listing-detail-handler") {
            \$slug = \$vars["slug"];
            \$htmlFile = __DIR__ . "/listing-detail/" . \$slug;
            if (!str_ends_with(\$slug, ".html")) \$htmlFile .= ".html";
            if (file_exists(\$htmlFile)) {
                header("Content-Type: text/html; charset=UTF-8");
                include \$htmlFile;
            } else {
                http_response_code(404);
                include "404.php";
            }
        } elseif (\$handler === "blogs-handler") {
            \$htmlFile = __DIR__ . "/blogs/" . \$vars["slug"];
            if (file_exists(\$htmlFile)) {
                header("Content-Type: text/html; charset=UTF-8");
                readfile(\$htmlFile);
            } elseif (file_exists(\$htmlFile . ".html")) {
                header("Content-Type: text/html; charset=UTF-8");
                readfile(\$htmlFile . ".html");
            } else {
                http_response_code(404);
                include "404.php";
            }
        } else {
            include __DIR__ . "/" . \$handler;
        }
        break;
}
EOD;
file_put_contents("router.php", $correct);
?>
