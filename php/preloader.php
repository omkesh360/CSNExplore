<!-- CSNExplore Preloader - OPTIMIZED FOR SPEED -->
<style>
#preloader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #0f172a; display: flex; align-items: center; justify-content: center; z-index: 99999; transition: opacity 0.25s ease, visibility 0.25s ease; }
#preloader.fade-out { opacity: 0; visibility: hidden; }

#preloader-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  padding: 1rem;
  text-align: center;
  width: 100%;
}

#preloader-logo {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.875rem;
}

#preloader-logo img {
  height: 40px;
  max-width: 90vw;
  object-fit: contain;
  animation: pulseLogo 1.5s infinite ease-in-out;
}

@media (min-width: 768px) {
  #preloader-logo img {
    height: 50px;
  }
}

.preloader-tagline {
  color: #cbd5e1;
  font-family: 'Inter', sans-serif;
  font-size: 0.75rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  font-weight: 600;
  margin: 0;
  opacity: 0.9;
}

@media (min-width: 768px) {
  .preloader-tagline {
    font-size: 0.85rem;
  }
}

@keyframes pulseLogo {
  0%, 100% { transform: scale(1); opacity: 0.9; }
  50% { transform: scale(1.05); opacity: 1; }
}

/* Simplified loader - FASTER animations */
.pl {
  display: block;
  width: 6em;
  height: 6em;
}

.pl__arrows,
.pl__ring-rotate,
.pl__ring-stroke,
.pl__tick {
  animation-duration: 1.5s !important;
  animation-timing-function: linear !important;
  animation-iteration-count: infinite !important;
}

.pl__arrows {
  animation-name: arrows42 !important;
  transform: rotate(45deg) !important;
  transform-origin: 16px 52px !important;
}

.pl__ring-rotate,
.pl__ring-stroke {
  transform-origin: 80px 80px !important;
}

.pl__ring-rotate {
  animation-name: ringRotate42 !important;
}

.pl__ring-stroke {
  animation-name: ringStroke42 !important;
  transform: rotate(-45deg) !important;
}

.pl__tick {
  animation-name: tick42 !important;
}

.pl__tick:nth-child(2) { animation-delay: -1.31s; }
.pl__tick:nth-child(3) { animation-delay: -1.13s; }
.pl__tick:nth-child(4) { animation-delay: -0.94s; }
.pl__tick:nth-child(5) { animation-delay: -0.75s; }
.pl__tick:nth-child(6) { animation-delay: -0.56s; }
.pl__tick:nth-child(7) { animation-delay: -0.38s; }
.pl__tick:nth-child(8) { animation-delay: -0.19s; }

/* Animations */
@keyframes arrows42 {
  from { transform: rotate(45deg); }
  to { transform: rotate(405deg); }
}

@keyframes ringRotate42 {
  from { transform: rotate(0deg); }
  to { transform: rotate(720deg); }
}

@keyframes ringStroke42 {
  from, to {
    stroke-dashoffset: 452;
    transform: rotate(-45deg);
  }
  50% {
    stroke-dashoffset: 169.5;
    transform: rotate(-180deg);
  }
}

@keyframes tick42 {
  from, 3%, 47%, to { stroke-dashoffset: -12; }
  14%, 36% { stroke-dashoffset: 0; }
}
</style>
<div id="preloader">
    <div id="preloader-content">
        <!-- Animated Loader -->
        <div id="preloader-svg">
		<svg class="pl" viewBox="0 0 160 160" width="160px" height="160px" xmlns="http://www.w3.org/2000/svg">
			<defs>
				<linearGradient id="grad" x1="0" y1="0" x2="0" y2="1">
					<stop offset="0%" stop-color="#000"></stop>
					<stop offset="100%" stop-color="#fff"></stop>
				</linearGradient>
				<mask id="mask1">
					<rect x="0" y="0" width="160" height="160" fill="url(#grad)"></rect>
				</mask>
				<mask id="mask2">
					<rect x="28" y="28" width="104" height="104" fill="url(#grad)"></rect>
				</mask>
			</defs>
			
			<g>
				<g class="pl__ring-rotate">
					<circle class="pl__ring-stroke" cx="80" cy="80" r="72" fill="none" stroke="#ec5b13" stroke-width="16" stroke-dasharray="452.39 452.39" stroke-dashoffset="452" stroke-linecap="round" transform="rotate(-45,80,80)"></circle>
				</g>
			</g>
			<g mask="url(#mask1)">
				<g class="pl__ring-rotate">
					<circle class="pl__ring-stroke" cx="80" cy="80" r="72" fill="none" stroke="#f97316" stroke-width="16" stroke-dasharray="452.39 452.39" stroke-dashoffset="452" stroke-linecap="round" transform="rotate(-45,80,80)"></circle>
				</g>
			</g>
			
			<g>
				<g stroke-width="4" stroke-dasharray="12 12" stroke-dashoffset="12" stroke-linecap="round" transform="translate(80,80)">
					<polyline class="pl__tick" stroke="hsl(223,10%,90%)" points="0,2 0,14" transform="rotate(-135,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,10%,90%)" points="0,2 0,14" transform="rotate(-90,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,10%,90%)" points="0,2 0,14" transform="rotate(-45,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,10%,90%)" points="0,2 0,14" transform="rotate(0,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,10%,90%)" points="0,2 0,14" transform="rotate(45,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,10%,90%)" points="0,2 0,14" transform="rotate(90,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,10%,90%)" points="0,2 0,14" transform="rotate(135,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,10%,90%)" points="0,2 0,14" transform="rotate(180,0,0) translate(0,40)"></polyline>
				</g>
			</g>
			<g mask="url(#mask1)">
				<g stroke-width="4" stroke-dasharray="12 12" stroke-dashoffset="12" stroke-linecap="round" transform="translate(80,80)">
					<polyline class="pl__tick" stroke="hsl(223,90%,80%)" points="0,2 0,14" transform="rotate(-135,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,90%,80%)" points="0,2 0,14" transform="rotate(-90,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,90%,80%)" points="0,2 0,14" transform="rotate(-45,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,90%,80%)" points="0,2 0,14" transform="rotate(0,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,90%,80%)" points="0,2 0,14" transform="rotate(45,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,90%,80%)" points="0,2 0,14" transform="rotate(90,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,90%,80%)" points="0,2 0,14" transform="rotate(135,0,0) translate(0,40)"></polyline>
					<polyline class="pl__tick" stroke="hsl(223,90%,80%)" points="0,2 0,14" transform="rotate(180,0,0) translate(0,40)"></polyline>
				</g>
			</g>
			
			<g>
				<g transform="translate(64,28)">
					<g class="pl__arrows" transform="rotate(45,16,52)">
						<path fill="#ec5b13" d="M17.998,1.506l13.892,43.594c.455,1.426-.56,2.899-1.998,2.899H2.108c-1.437,0-2.452-1.473-1.998-2.899L14.002,1.506c.64-2.008,3.356-2.008,3.996,0Z"></path>
						<path fill="hsl(223,10%,90%)" d="M14.009,102.499L.109,58.889c-.453-1.421,.559-2.889,1.991-2.889H29.899c1.433,0,2.444,1.468,1.991,2.889l-13.899,43.61c-.638,2.001-3.345,2.001-3.983,0Z"></path>
					</g>
				</g>
			</g>
			<g mask="url(#mask2)">
				<g transform="translate(64,28)">
					<g class="pl__arrows" transform="rotate(45,16,52)">
						<path fill="#f97316" d="M17.998,1.506l13.892,43.594c.455,1.426-.56,2.899-1.998,2.899H2.108c-1.437,0-2.452-1.473-1.998-2.899L14.002,1.506c.64-2.008,3.356-2.008,3.996,0Z"></path>
						<path fill="hsl(223,90%,80%)" d="M14.009,102.499L.109,58.889c-.453-1.421,.559-2.889,1.991-2.889H29.899c1.433,0,2.444,1.468,1.991,2.889l-13.899,43.61c-.638,2.001-3.345,2.001-3.983,0Z"></path>
					</g>
				</g>
			</g>
		</svg>
        </div>
        
        <!-- Website Logo and Tagline -->
        <div id="preloader-logo">
            <img src="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/images/travelhub.png" alt="CSNExplore">
            <p class="preloader-tagline">Your Premium Gateway to Maharashtra</p>
        </div>
    </div>
</div>
