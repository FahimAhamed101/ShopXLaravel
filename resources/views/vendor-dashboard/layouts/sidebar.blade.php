      <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
          <div class="container-fluid">
              <!-- BEGIN NAVBAR TOGGLER -->
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                  aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <!-- END NAVBAR TOGGLER -->
              <!-- BEGIN NAVBAR LOGO -->
              <div class="navbar-brand navbar-brand-autodark">
                  <a href="{{ route('vendor.dashboard') }}" aria-label="Tabler"><img
                          style="width: 100px; width: 100px; background: #fafafa; padding: 10px; border-radius: 5px;"
                          src="{{ asset(config('settings.site_logo')) }}" alt=""></a>
              </div>
              <!-- END NAVBAR LOGO -->
              <div class="navbar-nav flex-row d-lg-none">
                  <div class="nav-item d-none d-lg-flex me-3">
                      <div class="btn-list">
                          <a href="https://github.com/tabler/tabler" class="btn btn-5" target="_blank" rel="noreferrer">
                              <!-- Download SVG icon from http://tabler.io/icons/icon/brand-github -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round" class="icon icon-2">
                                  <path
                                      d="M9 19c-4.3 1.4 -4.3 -2.5 -6 -3m12 5v-3.5c0 -1 .1 -1.4 -.5 -2c2.8 -.3 5.5 -1.4 5.5 -6a4.6 4.6 0 0 0 -1.3 -3.2a4.2 4.2 0 0 0 -.1 -3.2s-1.1 -.3 -3.5 1.3a12.3 12.3 0 0 0 -6.2 0c-2.4 -1.6 -3.5 -1.3 -3.5 -1.3a4.2 4.2 0 0 0 -.1 3.2a4.6 4.6 0 0 0 -1.3 3.2c0 4.6 2.7 5.7 5.5 6c-.6 .6 -.6 1.2 -.5 2v3.5" />
                              </svg>
                              Source code
                          </a>
                          <a href="https://github.com/sponsors/codecalm" class="btn btn-6" target="_blank"
                              rel="noreferrer">
                              <!-- Download SVG icon from http://tabler.io/icons/icon/heart -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round" class="icon text-pink icon-2">
                                  <path
                                      d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                              </svg>
                              Sponsor
                          </a>
                      </div>
                  </div>
                  <div class="d-none d-lg-flex">
                      <div class="nav-item">
                          <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode"
                              data-bs-toggle="tooltip" data-bs-placement="bottom">
                              <!-- Download SVG icon from http://tabler.io/icons/icon/moon -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round" class="icon icon-1">
                                  <path
                                      d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                              </svg>
                          </a>
                          <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode"
                              data-bs-toggle="tooltip" data-bs-placement="bottom">
                              <!-- Download SVG icon from http://tabler.io/icons/icon/sun -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round" class="icon icon-1">
                                  <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                  <path
                                      d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                              </svg>
                          </a>
                      </div>
                      <div class="nav-item dropdown d-none d-md-flex">
                          <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                              aria-label="Show notifications" data-bs-auto-close="outside" aria-expanded="false">
                              <!-- Download SVG icon from http://tabler.io/icons/icon/bell -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round" class="icon icon-1">
                                  <path
                                      d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                  <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                              </svg>
                              <span class="badge bg-red"></span>
                          </a>
                          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                              <div class="card">
                                  <div class="card-header d-flex">
                                      <h3 class="card-title">Notifications</h3>
                                      <div class="btn-close ms-auto" data-bs-dismiss="dropdown"></div>
                                  </div>
                                  <div class="list-group list-group-flush list-group-hoverable">
                                      <div class="list-group-item">
                                          <div class="row align-items-center">
                                              <div class="col-auto"><span
                                                      class="status-dot status-dot-animated bg-red d-block"></span>
                                              </div>
                                              <div class="col text-truncate">
                                                  <a href="#" class="text-body d-block">Example 1</a>
                                                  <div class="d-block text-secondary text-truncate mt-n1">Change
                                                      deprecated html tags to text decoration classes (#29604)</div>
                                              </div>
                                              <div class="col-auto">
                                                  <a href="#" class="list-group-item-actions">
                                                      <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                          height="24" viewBox="0 0 24 24" fill="none"
                                                          stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          class="icon text-muted icon-2">
                                                          <path
                                                              d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                      </svg>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="list-group-item">
                                          <div class="row align-items-center">
                                              <div class="col-auto"><span class="status-dot d-block"></span></div>
                                              <div class="col text-truncate">
                                                  <a href="#" class="text-body d-block">Example 2</a>
                                                  <div class="d-block text-secondary text-truncate mt-n1">
                                                      justify-content:between ⇒ justify-content:space-between (#29734)
                                                  </div>
                                              </div>
                                              <div class="col-auto">
                                                  <a href="#" class="list-group-item-actions show">
                                                      <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                          height="24" viewBox="0 0 24 24" fill="none"
                                                          stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          class="icon text-yellow icon-2">
                                                          <path
                                                              d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                      </svg>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="list-group-item">
                                          <div class="row align-items-center">
                                              <div class="col-auto"><span class="status-dot d-block"></span></div>
                                              <div class="col text-truncate">
                                                  <a href="#" class="text-body d-block">Example 3</a>
                                                  <div class="d-block text-secondary text-truncate mt-n1">Update
                                                      change-version.js (#29736)</div>
                                              </div>
                                              <div class="col-auto">
                                                  <a href="#" class="list-group-item-actions">
                                                      <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                          height="24" viewBox="0 0 24 24" fill="none"
                                                          stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          class="icon text-muted icon-2">
                                                          <path
                                                              d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                      </svg>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="list-group-item">
                                          <div class="row align-items-center">
                                              <div class="col-auto"><span
                                                      class="status-dot status-dot-animated bg-green d-block"></span>
                                              </div>
                                              <div class="col text-truncate">
                                                  <a href="#" class="text-body d-block">Example 4</a>
                                                  <div class="d-block text-secondary text-truncate mt-n1">Regenerate
                                                      package-lock.json (#29730)</div>
                                              </div>
                                              <div class="col-auto">
                                                  <a href="#" class="list-group-item-actions">
                                                      <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                          height="24" viewBox="0 0 24 24" fill="none"
                                                          stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          class="icon text-muted icon-2">
                                                          <path
                                                              d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                      </svg>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="card-body">
                                      <div class="row">
                                          <div class="col">
                                              <a href="#" class="btn btn-2 w-100"> Archive all </a>
                                          </div>
                                          <div class="col">
                                              <a href="#" class="btn btn-2 w-100"> Mark all as read </a>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>

                  </div>
                  <div class="nav-item dropdown">
                      <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown"
                          aria-label="Open user menu">
                          <span class="avatar avatar-sm" style="background-image: url(./static/avatars/000m.jpg)">
                          </span>
                          <div class="d-none d-xl-block ps-2">
                              <div>Paweł Kuna</div>
                              <div class="mt-1 small text-secondary">UI Designer</div>
                          </div>
                      </a>
                      <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                          <a href="#" class="dropdown-item">Status</a>
                          <a href="./profile.html" class="dropdown-item">Profile</a>
                          <a href="#" class="dropdown-item">Feedback</a>
                          <div class="dropdown-divider"></div>
                          <a href="./settings.html" class="dropdown-item">Settings</a>
                          <a href="./sign-in.html" class="dropdown-item">Logout</a>
                      </div>
                  </div>
              </div>
              <div class="collapse navbar-collapse" id="sidebar-menu">
                  <!-- BEGIN NAVBAR MENU -->
                  <ul class="navbar-nav pt-lg-3">
                      <li class="nav-item">
                          <a class="nav-link {{ setActive(['vendor.dashboard', 'vendor.digital-products.edit']) }}"
                              href="{{ route('vendor.dashboard') }}">
                              <span class="nav-link-icon d-md-none d-lg-inline-block">
                                  <i class="ti ti-home"></i>
                              </span>
                              <span class="nav-link-title"> Home </span>
                          </a>
                      </li>
                      <li
                          class="nav-item dropdown {{ setActive(['vendor.products.*', 'vendor.digital-products.edit'], 'active') }}">
                          <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                              data-bs-auto-close="false" role="button" aria-expanded="false">
                              <span class="nav-link-icon d-md-none d-lg-inline-block">
                                  <i class="ti ti-shopping-cart"></i></span>
                              <span class="nav-link-title"> Ecommerce </span>
                          </a>
                          <div
                              class="dropdown-menu {{ setActive(['vendor.products.*', 'vendor.digital-products.edit'], 'show') }}">
                              <div class="dropdown-menu-columns">
                                  <div class="dropdown-menu-column">
                                      <div class="dropend">
                                          <a class="dropdown-item {{ setActive(['vendor.products.*', 'vendor.digital-products.edit'], 'active') }}"
                                              href="{{ route('vendor.products.index') }}">Products</a>
                                      </div>

                                  </div>

                              </div>
                          </div>
                      </li>
                      <li class="nav-item dropdown {{ setActive(['vendor.orders.*'], 'active') }}">
                          <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                              data-bs-auto-close="false" role="button" aria-expanded="false">
                              <span class="nav-link-icon d-md-none d-lg-inline-block">
                                  <i class="ti ti-shopping-bag"></i>
                              </span>
                              <span class="nav-link-title"> Orders </span>
                          </a>
                          <div class="dropdown-menu {{ setActive(['vendor.orders.*'], 'show') }}">
                              <div class="dropdown-menu-columns">
                                  <div class="dropdown-menu-column">
                                      <a class="dropdown-item" href="{{ route('vendor.orders.index') }}">
                                          All Orders
                                      </a>
                                  </div>

                                  <div class="dropdown-menu-column">
                                      <a class="dropdown-item"
                                          href="{{ route('vendor.orders.index', ['status' => 'pending']) }}">
                                          Pending Orders
                                      </a>
                                  </div>

                                  <div class="dropdown-menu-column">
                                      <a class="dropdown-item"
                                          href="{{ route('vendor.orders.index', ['status' => 'processed']) }}">
                                          Processed Orders
                                      </a>
                                  </div>

                                  <div class="dropdown-menu-column">
                                      <a class="dropdown-item"
                                          href="{{ route('vendor.orders.index', ['status' => 'packed']) }}">
                                          Packed Orders
                                      </a>
                                  </div>

                                  <div class="dropdown-menu-column">
                                      <a class="dropdown-item"
                                          href="{{ route('vendor.orders.index', ['status' => 'shipped']) }}">
                                          Shipped Orders
                                      </a>
                                  </div>
                              </div>
                          </div>
                      </li>
                      <li
                          class="nav-item dropdown {{ setActive(['vendor.withdraw-methods.*', 'vendor.withdraw-requests.*'], 'active') }}">
                          <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                              data-bs-auto-close="false" role="button" aria-expanded="false">
                              <span class="nav-link-icon d-md-none d-lg-inline-block">
                                  <i class="ti ti-basket-dollar"></i>
                              </span>
                              <span class="nav-link-title"> Withdraws </span>
                          </a>
                          <div
                              class="dropdown-menu {{ setActive(['vendor.withdraw-methods.*', 'vendor.withdraw-requests.*'], 'show') }}">
                              <div class="dropdown-menu-columns">
                                  <div class="dropdown-menu-column">
                                      <a class="dropdown-item {{ setActive(['vendor.withdraw-methods.*'], 'active') }}"
                                          href="{{ route('vendor.withdraw-methods.index') }}">
                                          Withdraw Methods
                                      </a>
                                  </div>
                                  <div class="dropdown-menu-column">
                                      <a class="dropdown-item {{ setActive(['vendor.withdraw-requests.*'], 'active') }}"
                                          href="{{ route('vendor.withdraw-requests.index') }}">
                                          Withdraw Requests
                                      </a>
                                  </div>


                              </div>
                          </div>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link {{ setActive(['vendor.store-profile.index'], 'active') }}"
                              href="{{ route('vendor.store-profile.index') }}">
                              <span
                                  class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                                  <i class="ti ti-user-scan"></i>
                              </span>
                              <span class="nav-link-title"> Store Profile </span>
                          </a>
                      </li>

                      <li class="nav-item">
                          <a class="nav-link {{ setActive(['vendor.profile.index'], 'active') }}"
                              href="{{ route('vendor.profile.index') }}">
                              <span
                                  class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                                  <i class="ti ti-user-circle"></i></span>
                              <span class="nav-link-title"> Settings </span>
                          </a>
                      </li>

                  </ul>
                  <!-- END NAVBAR MENU -->
              </div>
          </div>
      </aside>


      <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
          <div class="container-xl">
              <!-- BEGIN NAVBAR TOGGLER -->
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                  aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <!-- END NAVBAR TOGGLER -->
              <div class="navbar-nav flex-row order-md-last">
                  <div class="d-none d-md-flex">
                      <div class="nav-item">
                          <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" aria-label="Enable dark mode"
                              data-bs-original-title="Enable dark mode">
                              <!-- Download SVG icon from http://tabler.io/icons/icon/moon -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                  <path
                                      d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z">
                                  </path>
                              </svg>
                          </a>
                          <a href="?theme=light" class="nav-link px-0 hide-theme-light" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" aria-label="Enable light mode"
                              data-bs-original-title="Enable light mode">
                              <!-- Download SVG icon from http://tabler.io/icons/icon/sun -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                  <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                                  <path
                                      d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7">
                                  </path>
                              </svg>
                          </a>
                      </div>
                      {{-- <div class="nav-item dropdown d-none d-md-flex">
                          <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                              aria-label="Show notifications" data-bs-auto-close="outside" aria-expanded="false">
                              <!-- Download SVG icon from http://tabler.io/icons/icon/bell -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                  <path
                                      d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6">
                                  </path>
                                  <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                              </svg>
                              <span class="badge bg-red"></span>
                          </a>
                          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                              <div class="card">
                                  <div class="card-header d-flex">
                                      <h3 class="card-title">Notifications</h3>
                                      <div class="btn-close ms-auto" data-bs-dismiss="dropdown"></div>
                                  </div>
                                  <div class="list-group list-group-flush list-group-hoverable">
                                      <div class="list-group-item">
                                          <div class="row align-items-center">
                                              <div class="col-auto"><span
                                                      class="status-dot status-dot-animated bg-red d-block"></span>
                                              </div>
                                              <div class="col text-truncate">
                                                  <a href="#" class="text-body d-block">Example 1</a>
                                                  <div class="d-block text-secondary text-truncate mt-n1">Change
                                                      deprecated html tags to text decoration classes (#29604)</div>
                                              </div>
                                              <div class="col-auto">
                                                  <a href="#" class="list-group-item-actions">
                                                      <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                          height="24" viewBox="0 0 24 24" fill="none"
                                                          stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          class="icon text-muted icon-2">
                                                          <path
                                                              d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                          </path>
                                                      </svg>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="list-group-item">
                                          <div class="row align-items-center">
                                              <div class="col-auto"><span class="status-dot d-block"></span></div>
                                              <div class="col text-truncate">
                                                  <a href="#" class="text-body d-block">Example 2</a>
                                                  <div class="d-block text-secondary text-truncate mt-n1">
                                                      justify-content:between ⇒ justify-content:space-between (#29734)
                                                  </div>
                                              </div>
                                              <div class="col-auto">
                                                  <a href="#" class="list-group-item-actions show">
                                                      <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                          height="24" viewBox="0 0 24 24" fill="none"
                                                          stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          class="icon text-yellow icon-2">
                                                          <path
                                                              d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                          </path>
                                                      </svg>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="list-group-item">
                                          <div class="row align-items-center">
                                              <div class="col-auto"><span class="status-dot d-block"></span></div>
                                              <div class="col text-truncate">
                                                  <a href="#" class="text-body d-block">Example 3</a>
                                                  <div class="d-block text-secondary text-truncate mt-n1">Update
                                                      change-version.js (#29736)</div>
                                              </div>
                                              <div class="col-auto">
                                                  <a href="#" class="list-group-item-actions">
                                                      <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                          height="24" viewBox="0 0 24 24" fill="none"
                                                          stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          class="icon text-muted icon-2">
                                                          <path
                                                              d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                          </path>
                                                      </svg>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="list-group-item">
                                          <div class="row align-items-center">
                                              <div class="col-auto"><span
                                                      class="status-dot status-dot-animated bg-green d-block"></span>
                                              </div>
                                              <div class="col text-truncate">
                                                  <a href="#" class="text-body d-block">Example 4</a>
                                                  <div class="d-block text-secondary text-truncate mt-n1">Regenerate
                                                      package-lock.json (#29730)</div>
                                              </div>
                                              <div class="col-auto">
                                                  <a href="#" class="list-group-item-actions">
                                                      <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                          height="24" viewBox="0 0 24 24" fill="none"
                                                          stroke="currentColor" stroke-width="2"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          class="icon text-muted icon-2">
                                                          <path
                                                              d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                          </path>
                                                      </svg>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="card-body">
                                      <div class="row">
                                          <div class="col">
                                              <a href="#" class="btn btn-2 w-100"> Archive all </a>
                                          </div>
                                          <div class="col">
                                              <a href="#" class="btn btn-2 w-100"> Mark all as read </a>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div> --}}
                  </div>
                  <div class="nav-item dropdown">
                      <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown"
                          aria-label="Open user menu">
                          <span class="avatar avatar-sm" style="background-image: url({{ asset(user()->avatar) }})">
                          </span>
                          <div class="d-none d-xl-block ps-2">
                              <div>{{ user()->name }}</div>
                              <div class="mt-1 small text-secondary">Vendor</div>
                          </div>
                      </a>
                      <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                          <a href="{{ route('vendor.store-profile.index') }}" class="dropdown-item">Store Profile</a>
                          <div class="dropdown-divider"></div>
                          <a href="{{ route('vendor.profile.index') }}" class="dropdown-item">Settings</a>
                          <a onclick="event.preventDefault();
                                $('.logout-form').submit();"
                              href="" class="dropdown-item">Logout</a>
                          <form method="POST" action="{{ route('logout') }}" class="logout-form">
                              @csrf
                          </form>
                      </div>
                  </div>
              </div>
              <div class="collapse navbar-collapse" id="navbar-menu">
                  <!-- BEGIN NAVBAR MENU -->

                  <!-- END NAVBAR MENU -->
              </div>
          </div>
      </header>
