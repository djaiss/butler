<style lang="scss" scoped>
.grid {
  grid-template-columns: 200px 1fr 300px;
}

@media (max-width: 480px) {
  .grid {
    grid-template-columns: 1fr;
  }
}

.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

input[type='checkbox'] {
  top: 3px;
  width: 12px;
}
</style>

<template>
  <layout title="Dashboard" :inside-vault="true" :layout-data="layoutData">
    <main class="relative sm:mt-24">
      <div class="max-w-8xl mx-auto py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- left -->
          <div class="p-3 sm:p-0">
            <!-- favorites -->
            <favorites v-if="favorites.length > 0" :data="favorites" />

            <!-- last updated contacts -->
            <last-updated :data="lastUpdatedContacts" />
          </div>

          <!-- middle -->
          <div class="p-3 sm:p-0">
            <!-- actions -->
            <div style="background-color: #fcfeff" class="mb-6 rounded-lg border border-gray-200 bg-white">
              <div v-if="!addMode" class="p-3">
                <p class="mb-5 text-center"><span class="mr-2">👋</span> Good evening, Regis.</p>

                <div class="mb-2 justify-center sm:flex">
                  <pretty-button
                    :text="'life event'"
                    :icon="'plus'"
                    :classes="'mr-3'"
                    @click="showAddModal('lifeEvent')" />
                  <pretty-button :text="'activity'" :icon="'plus'" :classes="'mr-3'" />
                  <pretty-button :text="'entry'" :icon="'plus'" :classes="'mr-3'" />
                  <pretty-button :text="'mood'" :icon="'plus'" :classes="'mr-3'" />
                  <pretty-button :text="'communication'" :icon="'plus'" :classes="'mr-3'" />
                  <pretty-button :text="'goal'" :icon="'plus'" />
                </div>
              </div>

              <div v-if="addMode" class="p-5">
                <create-life-event @cancelled="addMode = false" />
              </div>
            </div>

            <!-- filters -->
            <div class="mb-3 text-right">
              <button
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
              </button>
            </div>

            <!-- feed -->
            <div class="mb-10 overflow-auto">
              <h3 class="mb-5 font-bold">Janvier 2010</h3>

              <!-- journal entry -->
              <entry />

              <feed-item />

              <feed-item />

              <!-- activity -->
              <activity />

              <!-- goal -->
              <goal />
            </div>

            <!-- archives -->
            <div>
              <p class="text-xs">Browse past entries</p>
              <ul>
                <li class="mr-2 inline text-sm">
                  <a class="underline" href="">2021</a> <span class="text-xs text-gray-500">(3)</span>
                </li>
                <li class="mr-2 inline text-sm">
                  <a class="underline" href="">2020</a> <span class="text-xs text-gray-500">(139)</span>
                </li>
                <li class="mr-2 inline text-sm">
                  <a class="underline" href="">2019</a> <span class="text-xs text-gray-500">(23)</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- right -->
          <div class="p-3 sm:p-0">
            <!-- upcoming reminders -->
            <upcoming-reminders :data="upcomingReminders" />

            <h3 class="mb-3 border-b border-gray-200 pb-1 font-medium">
              <span class="relative">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="icon-sidebar relative inline h-4 w-4 text-gray-300 hover:text-gray-600"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
              </span>
              Tasks
            </h3>
            <div class="relative mb-3 flex items-start">
              <input
                id="remember-me"
                name="remember-me"
                type="checkbox"
                class="focus:ring-3 relative h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
              <label for="remember-me" class="ml-2 block cursor-pointer text-sm text-gray-900">
                Remember mea sdfasdf asdf asdf asdf sdf
              </label>
            </div>
            <div class="flex items-start">
              <input
                id="remember-me"
                name="remember-me"
                type="checkbox"
                class="focus:ring-3 relative h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
              <label for="remember-me" class="ml-2 block cursor-pointer text-sm text-gray-900"> Remember me </label>
            </div>
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import SmallContact from '@/Shared/SmallContact';
import LastUpdated from '@/Pages/Vault/Dashboard/Partials/LastUpdated';
import UpcomingReminders from '@/Pages/Vault/Dashboard/Partials/UpcomingReminders';
import Favorites from '@/Pages/Vault/Dashboard/Partials/Favorites';
import Activity from '@/Pages/Vault/Dashboard/Partials/Feed/Activity';
import Entry from '@/Pages/Vault/Dashboard/Partials/Feed/Entry';
import Goal from '@/Pages/Vault/Dashboard/Partials/Feed/Goal';
import FeedItem from '@/Pages/Vault/Dashboard/Partials/Feed/FeedItem';
import CreateLifeEvent from '@/Pages/Vault/Dashboard/Partials/Feed/CreateLifeEvent';
import PrettyButton from '@/Shared/Form/PrettyButton';

export default {
  components: {
    Layout,
    LastUpdated,
    UpcomingReminders,
    Favorites,
    PrettyButton,
    SmallContact,
    Entry,
    Activity,
    Goal,
    FeedItem,
    CreateLifeEvent,
  },

  props: {
    layoutData: {
      type: Object,
      default: null,
    },
    lastUpdatedContacts: {
      type: Object,
      default: null,
    },
    upcomingReminders: {
      type: Object,
      default: null,
    },
    favorites: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      addMode: false,
    };
  },

  methods: {
    showAddModal(type) {
      if (type == 'lifeEvent') {
        this.addMode = true;
      }
    },
  },
};
</script>
