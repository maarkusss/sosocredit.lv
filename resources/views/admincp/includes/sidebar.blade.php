<div
    class="sidebar flex w-56 hidden"
    id="sidebar">
    <ul class="sidebar__ul">
        <x-admin-sidebar-li route_name="admincp.dashboard"
                            id="sidebar-dashboard"
                            title="Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
            </svg>
        </x-admin-sidebar-li>

        @can('view admins')
            <x-admin-sidebar-li route_name="admincp.admins.index"
                                id="sidebar-admins"
                                title="Admins">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M16.358 3.042a.833.833 0 0 0-.7-.167 6.667 6.667 0 0 1-5.183-1.058.833.833 0 0 0-.95 0 6.667 6.667 0 0 1-5.183 1.058.833.833 0 0 0-1.009.817V9.9a7.5 7.5 0 0 0 3.142 6.108l3.042 2.167a.833.833 0 0 0 .966 0l3.042-2.167A7.5 7.5 0 0 0 16.667 9.9V3.692a.833.833 0 0 0-.309-.65zM15 9.9a5.833 5.833 0 0 1-2.442 4.75L10 16.475 7.442 14.65A5.833 5.833 0 0 1 5 9.9V4.65a8.333 8.333 0 0 0 5-1.158 8.333 8.333 0 0 0 5 1.158V9.9z"/>
                </svg>
            </x-admin-sidebar-li>
        @endif

        @if (config('app.broker_enabled'))
            <x-admin-sidebar-li route_name="admincp.applications.applications.index"
                                id="sidebar-applications"
                                title="Applications">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M17.5 1.667h-15a.833.833 0 0 0-.833.833v15a.833.833 0 0 0 .833.833h15a.833.833 0 0 0 .833-.833v-15a.833.833 0 0 0-.833-.833zm-8.333 15H3.333v-3.334h5.834v3.334zm0-5H3.333V8.333h5.834v3.334zm7.5 5h-5.834v-3.334h5.834v3.334zm0-5h-5.834V8.333h5.834v3.334zm0-5H3.333V3.333h13.334v3.334z"/>
                </svg>
            </x-admin-sidebar-li>
        @endif

        @can('view bans')
            <x-admin-sidebar-li route_name="admincp.bans.index"
                                id="sidebar-bans"
                                title="Bans">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M10 1.667a8.333 8.333 0 1 0 0 16.666 8.333 8.333 0 0 0 0-16.666zm0 15A6.666 6.666 0 0 1 3.333 10a6.6 6.6 0 0 1 1.409-4.083l9.341 9.341A6.6 6.6 0 0 1 10 16.667zm5.258-2.584L5.917 4.742A6.6 6.6 0 0 1 10 3.333 6.667 6.667 0 0 1 16.667 10a6.6 6.6 0 0 1-1.409 4.083z"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

{{--        @if (count(available_locales()) > 1)--}}
{{--            <x-admin-sidebar-li route_name="admincp.connected-urls.index"--}}
{{--                                id="sidebar-connected-urls"--}}
{{--                                title="Connected URLs">--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">--}}
{{--                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />--}}
{{--                </svg>--}}
{{--            </x-admin-sidebar-li>--}}
{{--        @endif--}}

    <x-admin-sidebar-li route_name="admincp.blocks.index"
                        id="sidebar-blocks"
                        title="Blocks">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z" />
        </svg>
    </x-admin-sidebar-li>

        @can('view consumers')
            <x-admin-sidebar-li route_name="admincp.consumers.index"
                                id="sidebar-consumers"
                                title="Consumers">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M10.25 10.183a4.1 4.1 0 0 0 1.417-3.1 4.167 4.167 0 0 0-8.334 0 4.1 4.1 0 0 0 1.417 3.1A6.667 6.667 0 0 0 .833 16.25a.833.833 0 1 0 1.667 0 5 5 0 1 1 10 0 .833.833 0 1 0 1.667 0 6.666 6.666 0 0 0-3.917-6.067zm-2.75-.6a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm8.117.267A4.167 4.167 0 0 0 12.5 2.917a.833.833 0 0 0 0 1.666 2.5 2.5 0 0 1 2.5 2.5 2.499 2.499 0 0 1-1.25 2.159.834.834 0 0 0-.042 1.416l.325.217.109.058a5.834 5.834 0 0 1 3.333 5.317.833.833 0 1 0 1.667 0 7.501 7.501 0 0 0-3.525-6.4z"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

{{--        <x-admin-sidebar-li route_name="admincp.crons.index"--}}
{{--                            id="sidebar-crons"--}}
{{--                            title="Crons">--}}
{{--            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">--}}
{{--                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />--}}
{{--            </svg>--}}
{{--        </x-admin-sidebar-li>--}}

        @can('view faqs')
            <x-admin-sidebar-li route_name="admincp.faqs.index"
                                id="sidebar-faqs"
                                title="FAQs">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M2.5 10h5a.833.833 0 0 0 .833-.833v-5a.833.833 0 0 0-.833-.834h-5a.833.833 0 0 0-.833.834v5A.833.833 0 0 0 2.5 10zm.833-5h3.334v3.333H3.333V5zm7.5 1.667H17.5A.833.833 0 0 0 17.5 5h-6.667a.833.833 0 0 0 0 1.667zm0 8.333H2.5a.833.833 0 1 0 0 1.667h8.333a.833.833 0 1 0 0-1.667zm6.667-3.333h-15a.833.833 0 1 0 0 1.666h15a.833.833 0 0 0 0-1.666zm0-3.334h-6.667a.833.833 0 0 0 0 1.667H17.5a.833.833 0 0 0 0-1.667z"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

        @can('view news')
            <x-admin-sidebar-li route_name="admincp.news.index"
                                id="sidebar-news"
                                title="News">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M14.0264 5.96704H9.3078C8.89191 5.96704 8.55481 6.32185 8.55481 6.75966C8.55481 7.19743 8.89187 7.55228 9.3078 7.55228H14.0264C14.4426 7.55228 14.7794 7.19747 14.7794 6.75966C14.7794 6.32185 14.4423 5.96704 14.0264 5.96704Z"></path><path d="M15.8336 9.79785H9.3078C8.89191 9.79785 8.55481 10.1527 8.55481 10.5905C8.55481 11.0282 8.89187 11.3831 9.3078 11.3831H15.8336C16.2494 11.3831 16.5865 11.0283 16.5865 10.5905C16.5865 10.1527 16.2494 9.79785 15.8336 9.79785Z"></path><path d="M19.247 1H5.9194C5.50352 1 5.16642 1.3548 5.16642 1.79262V9.26949H3.8362C2.27226 9.26949 1 10.609 1 12.255V16.6671C1 18.3134 2.27226 19.6526 3.8362 19.6526C3.96419 19.6526 4.09021 19.6436 4.21345 19.6262H16.6367C18.4911 19.6262 20 18.038 20 16.0859V1.79262C20 1.3548 19.6629 1 19.247 1ZM5.16646 16.6671C5.16646 17.3464 4.70437 17.9144 4.09373 18.0409H3.83623V18.0673C3.10258 18.0673 2.50597 17.4391 2.50597 16.6671V12.255C2.50597 11.483 3.10284 10.8547 3.83623 10.8547H5.16649L5.16646 16.6671ZM18.4941 16.0859C18.4941 17.1456 17.6437 18.0409 16.6367 18.0409H6.35388C6.55743 17.6296 6.67239 17.1622 6.67239 16.6671V2.5852H18.4941V16.0859Z"></path><path d="M14.0264 13.3912H9.3078C8.89191 13.3912 8.55481 13.746 8.55481 14.1839C8.55481 14.6216 8.89187 14.9765 9.3078 14.9765H14.0264C14.4426 14.9765 14.7794 14.6217 14.7794 14.1839C14.7794 13.746 14.4423 13.3912 14.0264 13.3912Z" />
                </svg>
            </x-admin-sidebar-li>
        @endcan

        @if (config('app.go_rewrites_enabled'))
            @can('view go-rewrites')
                <x-admin-sidebar-li route_name="admincp.go-rewrites.index"
                                    id="sidebar-go-rewrites"
                                    title="Go Rewrites">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-5 h-5 mr-2 fill-current">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                </x-admin-sidebar-li>
            @endcan
        @endif

        <x-admin-sidebar-li route_name="admincp.images.index"
                            id="sidebar-images"
                            title="Images">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
        </x-admin-sidebar-li>

        @can('view lenders')
            <x-admin-sidebar-li route_name="admincp.lenders.index"
                                id="sidebar-lenders"
                                title="Lenders">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M7.917 8.75H10a.833.833 0 0 0 0-1.667h-.833v-.416a.833.833 0 1 0-1.667 0v.458a2.083 2.083 0 0 0 .417 4.125h.833a.417.417 0 0 1 0 .833H6.667a.833.833 0 1 0 0 1.667H7.5v.417a.833.833 0 1 0 1.667 0v-.459a2.083 2.083 0 0 0-.417-4.125h-.833a.417.417 0 0 1 0-.833zM17.5 10H15V2.5a.835.835 0 0 0-1.25-.725l-2.5 1.433-2.5-1.433a.833.833 0 0 0-.833 0l-2.5 1.433-2.5-1.433a.833.833 0 0 0-1.25.725v13.333a2.5 2.5 0 0 0 2.5 2.5h11.666a2.5 2.5 0 0 0 2.5-2.5v-5A.833.833 0 0 0 17.5 10zM4.167 16.667a.834.834 0 0 1-.834-.834V3.942L5 4.892a.9.9 0 0 0 .833 0l2.5-1.434 2.5 1.434a.9.9 0 0 0 .834 0l1.666-.95v11.891c.003.285.053.566.15.834H4.167zm12.5-.834a.834.834 0 0 1-1.667 0v-4.166h1.667v4.166z"/>
                </svg>
            </x-admin-sidebar-li>

            <x-admin-sidebar-li route_name="admincp.lenders.filters.index"
                                id="sidebar-lenders-filters"
                                title="Lender Filters">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                </svg>
            </x-admin-sidebar-li>

            <x-admin-sidebar-li route_name="admincp.lenders.providers.links.index"
                                id="sidebar-lenders-providers"
                                title="Providers">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2">
                    <g fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="1.5">
                        <path d="M17.07,20.61H9.79a2,2,0,0,1-2-2,2,2,0,0,1,2-2h1.87L5,9.86a2,2,0,0,1-.19-2.65,1.88,1.88,0,0,1,1.47-.68,1.84,1.84,0,0,1,1.35.55l4.06,4.06,4.08-3.06a1.91,1.91,0,0,1,2.5.18h0A17.18,17.18,0,0,1,22.42,15l.06.19"/>
                        <path d="M10.63,10.12A4.73,4.73,0,0,0,11,8.17,4.78,4.78,0,1,0,6.26,13a4.67,4.67,0,0,0,1.55-.26"/>
                    </g>
                </svg>
            </x-admin-sidebar-li>

            <x-admin-sidebar-li route_name="admincp.statuses.index"
                                id="sidebar-provider-priorities"
                                title="Statuses">
                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2">
                    <path d="M11.5757 1.42426C11.81 1.18995 12.1899 1.18995 12.4243 1.42426L22.5757 11.5757C22.81 11.81 22.8101 12.1899 22.5757 12.4243L12.4243 22.5757C12.19 22.81 11.8101 22.8101 11.5757 22.5757L1.42426 12.4243C1.18995 12.19 1.18995 11.8101 1.42426 11.5757L11.5757 1.42426Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 7L16 11M12 7L8 11.1667M12 7L12 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

        @can('view loantypes')
            <x-admin-sidebar-li route_name="admincp.loantypes.index"
                                id="sidebar-loantypes"
                                title="Loan types">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M2.5 10h5a.833.833 0 0 0 .833-.833v-5a.833.833 0 0 0-.833-.834h-5a.833.833 0 0 0-.833.834v5A.833.833 0 0 0 2.5 10zm.833-5h3.334v3.333H3.333V5zm7.5 1.667H17.5A.833.833 0 0 0 17.5 5h-6.667a.833.833 0 0 0 0 1.667zm0 8.333H2.5a.833.833 0 1 0 0 1.667h8.333a.833.833 0 1 0 0-1.667zm6.667-3.333h-15a.833.833 0 1 0 0 1.666h15a.833.833 0 0 0 0-1.666zm0-3.334h-6.667a.833.833 0 0 0 0 1.667H17.5a.833.833 0 0 0 0-1.667z"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

        <x-admin-sidebar-li route_name="admincp.mobile-apps.index"
                            id="sidebar-mobile-apps"
                            title="Mobile Apps">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
            </svg>
        </x-admin-sidebar-li>

        @if (config('postbacks.enabled'))
            @can('access postbacks')
                <x-admin-sidebar-li route_name="admincp.postbacks.index"
                                    id="sidebar-postbacks"
                                    title="Postbacks">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </x-admin-sidebar-li>
            @endcan
        @endif

        @can('view redirects')
            <x-admin-sidebar-li route_name="admincp.redirects.index"
                                id="sidebar-redirects"
                                title="Redirects">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.688c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062A1.125 1.125 0 013 16.81V8.688zM12.75 8.688c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062a1.125 1.125 0 01-1.683-.977V8.688z" />
                </svg>
            </x-admin-sidebar-li>
        @endcan

        @can('view reviews')
            <x-admin-sidebar-li route_name="admincp.reviews.index"
                                id="sidebar-reviews"
                                title="Reviews">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                </svg>
            </x-admin-sidebar-li>
        @endcan

        @can('view sections')
            <x-admin-sidebar-li route_name="admincp.sections.index"
                                id="sidebar-sections"
                                title="Sections">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M17.5 1.667h-15a.833.833 0 0 0-.833.833v15a.833.833 0 0 0 .833.833h15a.833.833 0 0 0 .833-.833v-15a.833.833 0 0 0-.833-.833zm-8.333 15H3.333v-3.334h5.834v3.334zm0-5H3.333V8.333h5.834v3.334zm7.5 5h-5.834v-3.334h5.834v3.334zm0-5h-5.834V8.333h5.834v3.334zm0-5H3.333V3.333h13.334v3.334z"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

        @can('view settings')
            <x-admin-sidebar-li route_name="admincp.settings.index"
                                id="sidebar-settings"
                                title="Settings">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M17.767 7.959l-1.575-.525.741-1.484a.834.834 0 0 0-.158-.95L15 3.225a.834.834 0 0 0-.958-.158l-1.484.742-.525-1.575a.834.834 0 0 0-.783-.567h-2.5a.833.833 0 0 0-.792.567l-.525 1.575-1.483-.742a.833.833 0 0 0-.95.158L3.225 5a.833.833 0 0 0-.158.959l.741 1.483-1.575.525a.833.833 0 0 0-.566.783v2.5a.833.833 0 0 0 .566.792l1.575.525-.741 1.483a.834.834 0 0 0 .158.95L5 16.775a.833.833 0 0 0 .958.159l1.484-.742.525 1.575a.834.834 0 0 0 .791.567h2.5a.834.834 0 0 0 .792-.567l.525-1.575 1.483.742a.833.833 0 0 0 .942-.159L16.775 15a.834.834 0 0 0 .158-.958l-.741-1.483 1.575-.525a.834.834 0 0 0 .566-.784v-2.5a.834.834 0 0 0-.566-.791zm-1.1 2.691l-1 .334a1.668 1.668 0 0 0-.967 2.35l.475.95-.917.916-.925-.5a1.667 1.667 0 0 0-2.325.967l-.333 1H9.35l-.333-1a1.667 1.667 0 0 0-2.35-.967l-.95.475-.917-.916.5-.925a1.667 1.667 0 0 0-.967-2.35l-1-.334v-1.3l1-.333a1.667 1.667 0 0 0 .967-2.35l-.475-.925.917-.917.925.475a1.667 1.667 0 0 0 2.35-.966l.333-1h1.3l.333 1a1.666 1.666 0 0 0 2.35.966l.95-.475.917.917-.5.925a1.667 1.667 0 0 0 .967 2.325l1 .333v1.325zM10 6.667a3.333 3.333 0 1 0 0 6.667 3.333 3.333 0 0 0 0-6.667zm0 5a1.667 1.667 0 1 1 0-3.334 1.667 1.667 0 0 1 0 3.334z"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

        @can('view statistics')
            <x-admin-sidebar-li route_name="admincp.statistics.index"
                                id="sidebar-statistics"
                                title="Statistics">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M17.5 16.667h-.833v-12.5a.833.833 0 1 0-1.667 0v12.5h-1.667V7.5a.833.833 0 1 0-1.666 0v9.167H10v-5.834a.833.833 0 1 0-1.667 0v5.834H6.667v-2.5a.833.833 0 1 0-1.667 0v2.5H3.333V2.5a.833.833 0 1 0-1.666 0v15a.833.833 0 0 0 .833.833h15a.833.833 0 0 0 0-1.666z"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

        @can('view visitors')
            <x-admin-sidebar-li route_name="admincp.visitors.index"
                                id="sidebar-visitors"
                                title="Visitors">
                <svg class="w-5 h-5 mr-2 fill-current">
                    <path d="M13.092 10.592a5 5 0 1 0-6.184 0 8.333 8.333 0 0 0-5.183 6.816.838.838 0 1 0 1.667.184 6.667 6.667 0 0 1 13.25 0 .833.833 0 0 0 .833.741h.092a.833.833 0 0 0 .733-.916 8.334 8.334 0 0 0-5.208-6.825zM10 10a3.333 3.333 0 1 1 0-6.667A3.333 3.333 0 0 1 10 10z"/>
                </svg>
            </x-admin-sidebar-li>
        @endcan

        <li class="sidebar__li">
            <div
                class="flex flex-col items-start justify-start px-2 text-gray-500 bg-white rounded hover:bg-gray-100 hover:text-gray-600"
                id="sidebar__language-dropdown">
                <div class="flex flex-row items-center justify-between w-full py-2"
                     id="dropdown-container">
                    <a href="#">
                        <div class="flex flex-row items-center">
                            <svg class="w-5 h-5 mr-2 fill-current">
                                <path d="M14.833 5.667H14v-2.5a2.5 2.5 0 0 0-2.5-2.5H3.167a2.5 2.5 0 0 0-2.5 2.5v10a.833.833 0 0 0 .516.766.833.833 0 0 0 .908-.175l2.342-2.35h1.234v1.2a2.5 2.5 0 0 0 2.5 2.5h5.766l1.975 1.984a.834.834 0 0 0 1.283-.13.833.833 0 0 0 .142-.462V8.167a2.5 2.5 0 0 0-2.5-2.5zm-9.166 2.5v1.575H4.092a.833.833 0 0 0-.592.241l-1.167 1.175V3.167a.833.833 0 0 1 .834-.834H11.5a.833.833 0 0 1 .833.834v2.5H8.167a2.5 2.5 0 0 0-2.5 2.5zm10 6.325l-.834-.834a.834.834 0 0 0-.591-.25H8.167a.833.833 0 0 1-.834-.833V8.167a.833.833 0 0 1 .834-.834h6.666a.833.833 0 0 1 .834.834v6.325z"/>
                            </svg>
                            <span class="font-medium text-normal">Language</span>
                        </div>
                    </a>
                    <div>
                        <svg class="w-5 h-5 fill-current"
                             id="dropdown-toggle">
                            <path d="M9.29302 12.95L10 13.657L15.657 8L14.243 6.586L10 10.828L5.75702 6.586L4.34302 8L9.29302 12.95Z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex flex-col hidden py-1 pl-6 ml-1"
                     id="dropdown">
                    @foreach (available_locales() as $locale)
                        <a href="{{ str_replace(app()->getLocale() . '/', $locale . '/', request()->server('REQUEST_URI')) }}"
                           class="py-1 text-gray-500 hover:text-gray-600">{{ strtoupper($locale) }}</a>
                    @endforeach
                </div>
            </div>
        </li>
    </ul>
</div>
<div class="sidebar-overlay hidden"
     id="sidebar-overlay"></div>
