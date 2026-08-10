@props(['name', 'size' => 24])
<svg {{ $attributes->merge(['class' => 'ui-icon', 'width' => $size, 'height' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
@switch($name)
@case('search')<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>@break
@case('menu')<path d="M4 7h16M4 12h16M4 17h16"/>@break
@case('close')<path d="m6 6 12 12M18 6 6 18"/>@break
@case('accessibility')<circle cx="12" cy="4" r="2"/><path d="M5 8h14M12 6v14M8 20l4-8 4 8"/>@break
@case('plus-text')<path d="M4 18V7h7M4 12h6M15 8h6M18 5v6"/>@break
@case('minus-text')<path d="M4 18V7h7M4 12h6M15 8h6"/>@break
@case('contrast')<circle cx="12" cy="12" r="9"/><path d="M12 3v18a9 9 0 0 0 0-18Z" fill="currentColor"/>@break
@case('moon')<path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"/>@break
@case('link')<path d="M10 13a5 5 0 0 0 7.1.1l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1M14 11a5 5 0 0 0-7.1-.1l-2 2A5 5 0 0 0 12 20l1.1-1.1"/>@break
@case('spacing')<path d="M4 7h16M4 17h16M8 12h8M5 10l-2 2 2 2M19 10l2 2-2 2"/>@break
@case('pause')<circle cx="12" cy="12" r="9" stroke-dasharray="2 3"/><path d="M9 9v6M15 9v6"/>@break
@case('image-off')<rect x="3" y="4" width="18" height="16" rx="2"/><path d="m3 16 5-5 9 9M14 11l2-2 5 5M4 3l17 18"/>@break
@case('dyslexia')<path d="M5 18V6h4.2a6 6 0 0 1 0 12H5ZM15 8h5M15 12h4M15 16h5"/>@break
@case('cursor')<path d="m5 3 13 8-6 1 3 6-3 1-3-6-4 4V3Z"/>@break
@case('line-height')<path d="M8 6h12M8 12h12M8 18h12M4 5v14M2 7l2-2 2 2M2 17l2 2 2-2"/>@break
@case('align')<path d="M5 6h14M5 10h10M5 14h14M5 18h8"/>@break
@case('saturation')<path d="M12 3s7 7 7 12a7 7 0 0 1-14 0c0-5 7-12 7-12Z"/><path d="M12 8v11a4 4 0 0 1 0-8" fill="currentColor"/>@break
@case('reset')<path d="M4 10a8 8 0 1 1 2 7M4 10V5M4 10h5"/>@break
@case('whatsapp')<path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.4-4.1A8 8 0 1 1 20 11.5Z"/><path d="M9 8c.4 3 2 4.7 5 5"/>@break
@case('instagram')<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/>@break
@case('tiktok')<path d="M14 4v10a4 4 0 1 1-3-3.9M14 4c.5 3 2.3 4.5 5 4.7"/>@break
@case('email')<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>@break
@case('phone')<path d="M6 3h3l1.5 4-2 1.5a15 15 0 0 0 7 7l1.5-2 4 1.5v3a3 3 0 0 1-3 3C10 20 4 14 3 6a3 3 0 0 1 3-3Z"/>@break
@case('location')<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>@break
@case('arrow-up')<path d="m6 10 6-6 6 6M12 4v16"/>@break
@case('helpdesk')<path d="M4 13v-2a8 8 0 0 1 16 0v2M4 13h3v6H5a2 2 0 0 1-2-2v-2a2 2 0 0 1 1-2ZM20 13h-3v6h1a2 2 0 0 0 2-2v-4ZM17 19c0 1-2 2-5 2"/>@break
@default<circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 2"/>@endswitch
</svg>
