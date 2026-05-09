<?php
$file = 'php/api/generate_html.php';
$content = file_get_contents($file);

$target1 = <<<'EOD'
            '.($type === 'cars' && !empty($item['driver_available']) && (float)($item['price_with_driver'] ?? 0) > 0 ? '
            <div class="space-y-2">
              <div class="flex items-baseline gap-1.5">
                <span class="text-xs font-semibold opacity-70">Self-Drive:</span>
                <span class="text-2xl font-black">₹'.number_format((float)$price_val).'</span>
                <span class="text-sm font-semibold opacity-80">'.$meta['unit'].'</span>
              </div>
              <div class="flex items-baseline gap-1.5">
                <span class="text-xs font-semibold opacity-70">With Driver:</span>
                <span class="text-2xl font-black">₹'.number_format((float)($item['price_with_driver'] ?? 0)).'</span>
                <span class="text-sm font-semibold opacity-80">'.$meta['unit'].'</span>
              </div>
            </div>' : '
            <div class="flex items-baseline gap-1.5">
              '.($price_val > 0 ? '<span class="text-xs font-semibold opacity-70 mr-0.5">from</span>' : '').'<span class="text-3xl font-black">'.$price_fmt.'</span>
              '.($meta['unit'] && $price_val > 0 ? '<span class="text-sm font-semibold opacity-80">'.htmlspecialchars($meta['unit']).'</span>' : '').'
            </div>').'
EOD;

$replace1 = <<<'EOD'
            '.(function() use ($type, $item, $price_val, $price_fmt, $meta) {
                if ($type === 'cars' && !empty($item['pricing_packages'])) {
                    $pkgs = json_decode($item['pricing_packages'], true);
                    $h = '<div class="space-y-2">';
                    if (isset($pkgs['flat_rate'])) $h .= '<div class="flex items-baseline gap-1.5"><span class="text-xs font-semibold opacity-70">Flat Daily Rate:</span><span class="text-2xl font-black">₹'.number_format($pkgs['flat_rate']).'</span><span class="text-sm font-semibold opacity-80">/ day</span></div>';
                    if (isset($pkgs['per_km'])) $h .= '<div class="flex items-baseline gap-1.5"><span class="text-xs font-semibold opacity-70">Per Km:</span><span class="text-2xl font-black">₹'.number_format($pkgs['per_km']).'</span><span class="text-sm font-semibold opacity-80">/ km '.htmlspecialchars($pkgs['per_km_note']??'').'</span></div>';
                    if (isset($pkgs['packages'])) {
                        foreach($pkgs['packages'] as $p) {
                            $h .= '<div class="flex items-baseline gap-1.5"><span class="text-xs font-semibold opacity-70">'.htmlspecialchars($p['name']).':</span><span class="text-xl font-black">₹'.number_format($p['price']).'</span><span class="text-sm font-semibold opacity-80">('.htmlspecialchars($p['limit']).')</span></div>';
                        }
                    }
                    if (isset($pkgs['extra_km'])) $h .= '<p class="text-[11px] font-medium opacity-80 mt-1">Extra mileage: ₹'.$pkgs['extra_km'].'/km</p>';
                    $h .= '</div>';
                    return $h;
                }
                elseif ($type === 'cars' && !empty($item['driver_available']) && (float)($item['price_with_driver'] ?? 0) > 0) {
                    return '<div class="space-y-2">
                      <div class="flex items-baseline gap-1.5">
                        <span class="text-xs font-semibold opacity-70">Self-Drive:</span>
                        <span class="text-2xl font-black">₹'.number_format((float)$price_val).'</span>
                        <span class="text-sm font-semibold opacity-80">'.$meta['unit'].'</span>
                      </div>
                      <div class="flex items-baseline gap-1.5">
                        <span class="text-xs font-semibold opacity-70">With Driver:</span>
                        <span class="text-2xl font-black">₹'.number_format((float)($item['price_with_driver'] ?? 0)).'</span>
                        <span class="text-sm font-semibold opacity-80">'.$meta['unit'].'</span>
                      </div>
                    </div>';
                } else {
                    return '<div class="flex items-baseline gap-1.5">
                      '.($price_val > 0 ? '<span class="text-xs font-semibold opacity-70 mr-0.5">from</span>' : '').'<span class="text-3xl font-black">'.$price_fmt.'</span>
                      '.($meta['unit'] && $price_val > 0 ? '<span class="text-sm font-semibold opacity-80">'.htmlspecialchars($meta['unit']).'</span>' : '').'
                    </div>';
                }
            })().'
EOD;

$target2 = <<<'EOD'
            '.($type === 'cars' && !empty($item['driver_available']) && (float)($item['price_with_driver'] ?? 0) > 0 ? '
            <div>
              <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-2">Rental Type *</label>
              <div class="space-y-2">
                <label class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-[#ec5b13] hover:bg-orange-50 transition-all">
                  <input type="radio" name="b-driver" value="0" checked class="w-4 h-4 text-[#ec5b13] border-slate-300 focus:ring-[#ec5b13]"/>
                  <div class="flex-1">
                    <p class="text-sm font-bold text-slate-900">Self-Drive</p>
                    <p class="text-xs text-slate-500">₹'.number_format((float)$price_val).' '.$meta['unit'].'</p>
                  </div>
                </label>
                <label class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-[#ec5b13] hover:bg-orange-50 transition-all">
                  <input type="radio" name="b-driver" value="1" class="w-4 h-4 text-[#ec5b13] border-slate-300 focus:ring-[#ec5b13]"/>
                  <div class="flex-1">
                    <p class="text-sm font-bold text-slate-900">With Driver</p>
                    <p class="text-xs text-slate-500">₹'.number_format((float)($item['price_with_driver'] ?? 0)).' '.$meta['unit'].'</p>
                  </div>
                </label>
              </div>
              <input type="hidden" id="b-driver" value="0"/>
            </div>' : '').'
EOD;

$replace2 = <<<'EOD'
            '.(function() use ($type, $item, $price_val, $meta) {
                if ($type === 'cars' && !empty($item['pricing_packages'])) {
                    $pkgs = json_decode($item['pricing_packages'], true);
                    $h = '<div><label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-2">Rental Package *</label><div class="space-y-2">';
                    $idx = 0;
                    if (isset($pkgs['flat_rate'])) {
                        $h .= '<label class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-[#ec5b13] hover:bg-orange-50 transition-all"><input type="radio" name="b-driver" value="Flat Rate" '.($idx++==0?'checked':'').' class="w-4 h-4 text-[#ec5b13] border-slate-300 focus:ring-[#ec5b13]"/><div class="flex-1"><p class="text-sm font-bold text-slate-900">Flat Daily Rate</p><p class="text-xs text-slate-500">₹'.number_format($pkgs['flat_rate']).' / day</p></div></label>';
                    }
                    if (isset($pkgs['per_km'])) {
                        $h .= '<label class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-[#ec5b13] hover:bg-orange-50 transition-all"><input type="radio" name="b-driver" value="Per Km Rate" '.($idx++==0?'checked':'').' class="w-4 h-4 text-[#ec5b13] border-slate-300 focus:ring-[#ec5b13]"/><div class="flex-1"><p class="text-sm font-bold text-slate-900">Per Km Rate '.htmlspecialchars($pkgs['per_km_note']??'').'</p><p class="text-xs text-slate-500">₹'.number_format($pkgs['per_km']).' / km</p></div></label>';
                    }
                    if (isset($pkgs['packages'])) {
                        foreach($pkgs['packages'] as $p) {
                            $h .= '<label class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-[#ec5b13] hover:bg-orange-50 transition-all"><input type="radio" name="b-driver" value="'.htmlspecialchars($p['name']).'" '.($idx++==0?'checked':'').' class="w-4 h-4 text-[#ec5b13] border-slate-300 focus:ring-[#ec5b13]"/><div class="flex-1"><p class="text-sm font-bold text-slate-900">'.htmlspecialchars($p['name']).'</p><p class="text-xs text-slate-500">₹'.number_format($p['price']).' ('.htmlspecialchars($p['limit']).')</p></div></label>';
                        }
                    }
                    $h .= '</div></div>';
                    return $h;
                }
                elseif ($type === 'cars' && !empty($item['driver_available']) && (float)($item['price_with_driver'] ?? 0) > 0) {
                    return '<div>
                      <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-2">Rental Type *</label>
                      <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-[#ec5b13] hover:bg-orange-50 transition-all">
                          <input type="radio" name="b-driver" value="Self-Drive" checked class="w-4 h-4 text-[#ec5b13] border-slate-300 focus:ring-[#ec5b13]"/>
                          <div class="flex-1">
                            <p class="text-sm font-bold text-slate-900">Self-Drive</p>
                            <p class="text-xs text-slate-500">₹'.number_format((float)$price_val).' '.$meta['unit'].'</p>
                          </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-[#ec5b13] hover:bg-orange-50 transition-all">
                          <input type="radio" name="b-driver" value="With Driver" class="w-4 h-4 text-[#ec5b13] border-slate-300 focus:ring-[#ec5b13]"/>
                          <div class="flex-1">
                            <p class="text-sm font-bold text-slate-900">With Driver</p>
                            <p class="text-xs text-slate-500">₹'.number_format((float)($item['price_with_driver'] ?? 0)).' '.$meta['unit'].'</p>
                          </div>
                        </label>
                      </div>
                    </div>';
                }
                return '';
            })().'
EOD;

$target1 = str_replace("\r\n", "\n", $target1);
$target2 = str_replace("\r\n", "\n", $target2);
$content = str_replace("\r\n", "\n", $content);

$content = str_replace($target1, $replace1, $content);
$content = str_replace($target2, $replace2, $content);

file_put_contents($file, $content);
echo "Replacements done.";
