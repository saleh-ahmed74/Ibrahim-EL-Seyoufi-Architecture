<div class="social-icon-list">
            @foreach (['instagram' => $settings?->instagram_url, 'facebook' => $settings?->facebook_url, 'linkedin' => $settings?->linkedin_url, 'youtube' => $settings?->youtube_url, 'pinterest' => $settings?->pinterest_url] as $network => $url)
                @if ($url)
                    <a class="social-link social-{{ $network }}" href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network) }}" title="{{ ucfirst($network) }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            @switch($network)
                                @case('instagram')<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="18" cy="6" r=".8" fill="currentColor" stroke="none"/>@break
                                @case('facebook')<path d="M14 8h3V4h-3a5 5 0 0 0-5 5v3H6v4h3v4h4v-4h3l1-4h-4V9a1 1 0 0 1 1-1Z" fill="currentColor" stroke="none"/>@break
                                @case('linkedin')<path d="M5 8.5A2.25 2.25 0 1 0 5 4a2.25 2.25 0 0 0 0 4.5ZM3 10h4v11H3zm6 0h4v1.6c.7-1.1 1.8-1.9 3.6-1.9 3.2 0 4.4 2 4.4 5.1V21h-4v-5.5c0-1.5-.4-2.5-1.8-2.5s-2.2 1-2.2 2.5V21H9z" fill="currentColor" stroke="none"/>@break
                                @case('youtube')<path d="M22 8.2a3 3 0 0 0-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 0 0 2 8.2 31 31 0 0 0 1.5 12a31 31 0 0 0 .5 3.8 3 3 0 0 0 2.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 22.5 12a31 31 0 0 0-.5-3.8ZM10 15.2V8.8l5.5 3.2z" fill="currentColor" stroke="none"/>@break
                                @case('pinterest')<path d="M12 2a10 10 0 0 0-3.6 19.3c0-.8 0-1.8.2-2.7l1.3-5.4s-.3-.6-.3-1.5c0-1.4.8-2.5 1.8-2.5.9 0 1.3.7 1.3 1.5 0 .9-.6 2.2-.9 3.5-.3 1 .5 1.8 1.5 1.8 1.8 0 3.2-1.9 3.2-4.6 0-2.4-1.7-4.1-4.2-4.1-2.9 0-4.6 2.2-4.6 4.4 0 .9.3 1.9.8 2.4.1.1.1.2.1.4l-.3 1.1c0 .2-.2.3-.4.2-1.3-.6-2.1-2.3-2.1-3.7 0-3 2.2-5.8 6.5-5.8 3.4 0 6 2.4 6 5.6 0 3.3-2.1 6.1-5.1 6.1-1 0-2-.6-2.3-1.2l-.7 2.7c-.2 1-.9 2-1.3 2.7A10 10 0 1 0 12 2Z" fill="currentColor" stroke="none"/>@break
                            @endswitch
                        </svg>
                    </a>
                @endif
            @endforeach
</div>
