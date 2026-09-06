@props([
    'class' => 'w-9 h-9',
    'variant' => 'badge', // 'badge' (with squircle container) or 'icon' (pure vector)
    'showText' => false,
    'textSize' => 'text-base',
])

<div class="inline-flex items-center gap-2.5 shrink-0">
    @if($variant === 'badge')
        {{-- Luxury Squircle Brand Badge --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="{{ $class }} shrink-0" aria-label="SwanFlow Logo">
            <defs>
                <linearGradient id="logoBg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#020617"/>
                    <stop offset="55%" stop-color="#061224"/>
                    <stop offset="100%" stop-color="#03251c"/>
                </linearGradient>
                <linearGradient id="logoRing" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#34d399" stop-opacity="0.6"/>
                    <stop offset="50%" stop-color="#14b8a6" stop-opacity="0.15"/>
                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.7"/>
                </linearGradient>
                <linearGradient id="logoSwanBody" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#6ee7b7"/>
                    <stop offset="25%" stop-color="#34d399"/>
                    <stop offset="65%" stop-color="#10b981"/>
                    <stop offset="100%" stop-color="#059669"/>
                </linearGradient>
                <linearGradient id="logoWing1" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#059669"/>
                    <stop offset="45%" stop-color="#10b981"/>
                    <stop offset="80%" stop-color="#34d399"/>
                    <stop offset="100%" stop-color="#a7f3d0"/>
                </linearGradient>
                <linearGradient id="logoWing2" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#0d9488"/>
                    <stop offset="50%" stop-color="#14b8a6"/>
                    <stop offset="100%" stop-color="#5eead4"/>
                </linearGradient>
                <linearGradient id="logoWing3" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#115e59"/>
                    <stop offset="50%" stop-color="#0f766e"/>
                    <stop offset="100%" stop-color="#2dd4bf"/>
                </linearGradient>
                <linearGradient id="logoWater" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#0d9488" stop-opacity="0"/>
                    <stop offset="40%" stop-color="#14b8a6" stop-opacity="0.8"/>
                    <stop offset="70%" stop-color="#10b981" stop-opacity="0.5"/>
                    <stop offset="100%" stop-color="#059669" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="logoBeak" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fbbf24"/>
                    <stop offset="100%" stop-color="#d97706"/>
                </linearGradient>
                <filter id="logoGlow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="0" dy="8" stdDeviation="16" flood-color="#10b981" flood-opacity="0.38"/>
                </filter>
            </defs>
            <rect width="512" height="512" rx="124" fill="url(#logoBg)"/>
            <rect x="16" y="16" width="480" height="480" rx="112" fill="none" stroke="url(#logoRing)" stroke-width="2.5"/>
            <g filter="url(#logoGlow)" transform="translate(256, 268) scale(0.88) translate(-256, -256)">
                <path d="M 115 400 C 185 385, 260 400, 330 390 C 385 382, 425 368, 440 358 C 405 390, 335 410, 250 410 C 175 410, 130 402, 115 400 Z" fill="url(#logoWater)"/>
                <path d="M 235 358 C 285 350, 345 330, 390 288 C 424 256, 440 220, 444 185 C 430 228, 396 278, 338 318 C 290 350, 248 358, 235 358 Z" fill="url(#logoWing3)"/>
                <path d="M 215 335 C 265 320, 332 278, 384 218 C 418 172, 435 122, 438 82 C 422 132, 382 198, 322 254 C 268 304, 226 328, 215 335 Z" fill="url(#logoWing2)"/>
                <path d="M 200 305 C 242 272, 310 220, 368 145 C 408 90, 424 40, 426 15 C 408 60, 362 135, 296 208 C 244 265, 212 292, 200 305 Z" fill="url(#logoWing1)"/>
                <path d="M 155 360 C 120 335, 110 280, 135 225 C 152 188, 185 160, 205 125 C 218 102, 222 75, 215 55 C 210 40, 198 32, 185 36 C 172 40, 164 52, 162 65 C 160 78, 168 90, 180 92 C 160 88, 142 75, 144 52 C 146 30, 168 15, 198 16 C 230 18, 248 38, 246 72 C 244 105, 225 138, 200 175 C 175 212, 155 248, 162 290 C 170 335, 210 360, 260 365 C 300 368, 340 355, 365 340 C 325 375, 265 390, 205 385 C 175 382, 160 372, 155 360 Z" fill="url(#logoSwanBody)"/>
                <path d="M 166 52 L 130 68 L 160 76 Z" fill="url(#logoBeak)"/>
                <circle cx="178" cy="48" r="3.5" fill="#020617"/>
                <circle cx="426" cy="15" r="4.5" fill="#ffffff"/>
                <circle cx="426" cy="15" r="11" fill="#a7f3d0" opacity="0.6"/>
            </g>
        </svg>
    @else
        {{-- Pure Vector Icon (Ideal for colored/gradient surfaces) --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="90 10 360 410" class="{{ $class }} shrink-0" aria-label="SwanFlow Brand Mark">
            <defs>
                <linearGradient id="pureSwanBody" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#a7f3d0"/>
                    <stop offset="30%" stop-color="#34d399"/>
                    <stop offset="70%" stop-color="#10b981"/>
                    <stop offset="100%" stop-color="#059669"/>
                </linearGradient>
                <linearGradient id="pureWing1" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#10b981"/>
                    <stop offset="50%" stop-color="#34d399"/>
                    <stop offset="100%" stop-color="#ffffff"/>
                </linearGradient>
                <linearGradient id="pureWing2" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#0d9488"/>
                    <stop offset="50%" stop-color="#14b8a6"/>
                    <stop offset="100%" stop-color="#5eead4"/>
                </linearGradient>
                <linearGradient id="pureWing3" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#115e59"/>
                    <stop offset="60%" stop-color="#0f766e"/>
                    <stop offset="100%" stop-color="#2dd4bf"/>
                </linearGradient>
                <linearGradient id="pureBeak" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fbbf24"/>
                    <stop offset="100%" stop-color="#f59e0b"/>
                </linearGradient>
            </defs>
            <path d="M 115 400 C 185 385, 260 400, 330 390 C 385 382, 425 368, 440 358 C 405 390, 335 410, 250 410 C 175 410, 130 402, 115 400 Z" fill="#14b8a6" opacity="0.6"/>
            <path d="M 235 358 C 285 350, 345 330, 390 288 C 424 256, 440 220, 444 185 C 430 228, 396 278, 338 318 C 290 350, 248 358, 235 358 Z" fill="url(#pureWing3)"/>
            <path d="M 215 335 C 265 320, 332 278, 384 218 C 418 172, 435 122, 438 82 C 422 132, 382 198, 322 254 C 268 304, 226 328, 215 335 Z" fill="url(#pureWing2)"/>
            <path d="M 200 305 C 242 272, 310 220, 368 145 C 408 90, 424 40, 426 15 C 408 60, 362 135, 296 208 C 244 265, 212 292, 200 305 Z" fill="url(#pureWing1)"/>
            <path d="M 155 360 C 120 335, 110 280, 135 225 C 152 188, 185 160, 205 125 C 218 102, 222 75, 215 55 C 210 40, 198 32, 185 36 C 172 40, 164 52, 162 65 C 160 78, 168 90, 180 92 C 160 88, 142 75, 144 52 C 146 30, 168 15, 198 16 C 230 18, 248 38, 246 72 C 244 105, 225 138, 200 175 C 175 212, 155 248, 162 290 C 170 335, 210 360, 260 365 C 300 368, 340 355, 365 340 C 325 375, 265 390, 205 385 C 175 382, 160 372, 155 360 Z" fill="url(#pureSwanBody)"/>
            <path d="M 166 52 L 130 68 L 160 76 Z" fill="url(#pureBeak)"/>
            <circle cx="178" cy="48" r="3.5" fill="#ffffff" opacity="0.9"/>
            <circle cx="426" cy="15" r="4.5" fill="#ffffff"/>
            <circle cx="426" cy="15" r="11" fill="#a7f3d0" opacity="0.6"/>
        </svg>
    @endif

    @if($showText)
        <span class="{{ $textSize }} font-extrabold tracking-tight text-slate-900 dark:text-white">SwanFlow</span>
    @endif
</div>
