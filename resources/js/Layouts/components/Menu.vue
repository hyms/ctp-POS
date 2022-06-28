<template>
    <div>
        <v-navigation-drawer
            v-model="isDrawerOpen"
            app
            temporary
            class="app-navigation-menu"
        >
            <!-- Navigation Header -->
            <div class="vertical-nav-header d-flex items-center ps-6 pe-5 pt-5 pb-2">
                <Link
                    href="/"
                    class="d-flex align-center text-decoration-none"
                >
                    <v-img
                        src=""
                        max-height="30px"
                        max-width="30px"
                        alt="logo"
                        contain
                        eager
                        class="app-logo me-3"
                    ></v-img>
                    <v-slide-x-transition>
                        <h2 class="app-title text--primary">
                            MATERIO
                        </h2>
                    </v-slide-x-transition>
                </Link>
            </div>

            <!-- Navigation Items -->
            <v-list
                expand
                shaped
                class="vertical-nav-menu-items pr-5"
            >
                <v-list-item
                    class="vertical-nav-menu-link"
                    active-class="bg-gradient-primary white--text"
                >
                    <v-list-item-title>
                        Dashboard
                    </v-list-item-title>
                </v-list-item>


                <v-list-group
                    ref="refVListGroup"
                    class="vertical-nav-menu-group text--primary"
                >
                    <template #activator>
                        <v-list-item-title>
                            group
                        </v-list-item-title>
                    </template>

                    <v-list-item
                        class="vertical-nav-menu-link"
                        active-class="bg-gradient-primary white--text"
                    >
                        <v-list-item-title>
                            Account Settings
                        </v-list-item-title>
                    </v-list-item>
                </v-list-group>
                <v-subheader>
    <span
        class="title-wrapper"
    >
      <span>USER INTERFACE</span>
    </span>
                </v-subheader>
                <v-list-item
                    class="vertical-nav-menu-link"
                    active-class="bg-gradient-primary white--text"
                >
                    <v-list-item-title>
                        Typography
                    </v-list-item-title>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar
            app
            flat
            absolute
            color="transparent"
        >
            <div class="boxed-container w-full">
                <div class="d-flex align-center mx-6">
                    <!-- Left Content -->
                    <v-app-bar-nav-icon
                        class="d-block d-lg-none me-2"
                        @click="isDrawerOpen = !isDrawerOpen"
                    ></v-app-bar-nav-icon>
                    <v-text-field
                        rounded
                        dense
                        outlined
                        class="app-bar-search flex-grow-0"
                        hide-details
                    ></v-text-field>

                    <v-spacer></v-spacer>

                    <!-- Right Content -->
                    <app-bar-user-menu></app-bar-user-menu>
                </div>
            </div>
        </v-app-bar>
    </div>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import AppBarUserMenu from './AppBarUserMenu.vue'

export default {
    components:{
        AppBarUserMenu,
    },
    data() {
        return {
            isDrawerOpen: null,
        }
    },
}
</script>

<style lang="scss" scoped>
.app-title {
    font-size: 1.25rem;
    font-weight: 700;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: 0.3px;
}

// ? Adjust this `translateX` value to keep logo in center when vertical nav menu is collapsed (Value depends on your logo)
.app-logo {
    transition: all 0.18s ease-in-out;

    .v-navigation-drawer--mini-variant & {
        transform: translateX(-4px);
    }
}

.app-navigation-menu {
    .v-list-item {
        &.vertical-nav-menu-link {
            ::v-deep .v-list-item__icon {
                .v-icon {
                    transition: none !important;
                }
            }
        }
    }
}

// You can remove below style
// Upgrade Banner
.app-navigation-menu {
    .upgrade-banner {
        position: absolute;
        bottom: 13px;
        left: 50%;
        transform: translateX(-50%);
    }
}

.v-app-bar ::v-deep {
    .v-toolbar__content {
        padding: 0;

        .app-bar-search {
            .v-input__slot {
                padding-left: 18px;
            }
        }
    }
}

//.boxed-container {
//    max-width: 1440px;
//    margin-left: auto;
//    margin-right: auto;
//}
</style>
