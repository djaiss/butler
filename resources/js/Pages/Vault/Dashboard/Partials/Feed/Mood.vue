<style lang="scss" scoped>
.icon-date {
  top: -1px;
}
</style>

<template>
  <div class="relative border border-gray-300 bg-white sm:rounded-lg">
    <span class="absolute rounded border bg-blue-50 px-2 py-0 text-xs text-blue-500" style="top: -10px; left: 10px">
      mood
    </span>
    <div class="p-3 pt-5" />
    <div class="flex justify-between border-t border-gray-200 px-3 py-2 text-xs text-gray-600">
      <div>
        <div class="relative mr-3 inline">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="icon-date relative inline h-4 w-4 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Sept 12, 2020
        </div>
      </div>
      <div>
        <hover-menu />
      </div>
    </div>
  </div>
</template>

<script>
import HoverMenu from '@/Shared/HoverMenu';

export default {
  components: {
    HoverMenu,
  },
};
</script>
