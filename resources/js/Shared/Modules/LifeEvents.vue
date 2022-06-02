<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

.item-list {
  &:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  &:last-child {
    border-bottom: 0;
  }

  &:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}

select {
  padding-left: 8px;
  padding-right: 20px;
  background-position: right 3px center;
}
</style>

<template>
  <div class="mb-10">
    <!-- title + cta -->
    <div class="mb-3 items-center justify-between border-b border-gray-200 pb-2 sm:flex">
      <div class="mb-2 sm:mb-0">
        <span class="relative mr-1">
          <span class="relative mr-1">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="icon-sidebar relative inline h-4 w-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
            </svg>
          </span>
        </span>

        <span class="font-semibold">Life events</span>
      </div>
      <pretty-button :text="'Add a pet'" :icon="'plus'" :classes="'sm:w-fit w-full'" @click="showCreatePetModal" />
    </div>

    <!-- add a pet modal -->
    <form v-if="addPetModalShown" class="bg-form mb-6 rounded-lg border border-gray-200" @submit.prevent="submit()">
      <div class="border-b border-gray-200">
        <div v-if="form.errors.length > 0" class="p-5">
          <errors :errors="form.errors" />
        </div>

        <!-- name -->
        <div class="border-b border-gray-200 p-5">
          <text-input
            :ref="'newName'"
            v-model="form.name"
            :label="'Name of the pet'"
            :type="'text'"
            :autofocus="true"
            :input-class="'block w-full'"
            :required="false"
            :autocomplete="false"
            :maxlength="255"
            @esc-key-pressed="addPetModalShown = false" />
        </div>

        <div class="p-5">
          <!-- pet categories -->
          <dropdown
            v-model="form.pet_category_id"
            :data="data.pet_categories"
            :required="true"
            :placeholder="'Choose a value'"
            :dropdown-class="'block w-full'"
            :label="'Pet category'" />
        </div>
      </div>

      <div class="flex justify-between p-5">
        <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="addPetModalShown = false" />
        <pretty-button :text="'Add pet'" :state="loadingState" :icon="'plus'" :classes="'save'" />
      </div>
    </form>

    <!-- pets -->
    <div v-if="localPets.length > 0">
      <ul class="mb-4 rounded-lg border border-gray-200 bg-white">
        <li v-for="pet in localPets" :key="pet.id" class="item-list border-b border-gray-200 hover:bg-slate-50">
          <!-- pet -->
          <div v-if="editedPetId != pet.id" class="flex items-center justify-between px-3 py-2">
            <div class="flex items-center">
              <span class="mr-2 text-sm text-gray-500">{{ pet.pet_category.name }}</span>
              <span class="mr-2">{{ pet.name }}</span>
            </div>

            <!-- actions -->
            <ul class="text-sm">
              <li class="mr-4 inline cursor-pointer text-blue-500 hover:underline" @click="showEditPetModal(pet)">
                Edit
              </li>
              <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(pet)">Delete</li>
            </ul>
          </div>

          <!-- edit pet modal -->
          <form v-if="editedPetId == pet.id" class="bg-form" @submit.prevent="update(pet)">
            <div class="border-b border-gray-200">
              <div v-if="form.errors.length > 0" class="p-5">
                <errors :errors="form.errors" />
              </div>

              <!-- name -->
              <div class="border-b border-gray-200 p-5">
                <text-input
                  :ref="'label'"
                  v-model="form.name"
                  :label="'Name of the pet'"
                  :type="'text'"
                  :autofocus="true"
                  :input-class="'block w-full'"
                  :required="false"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="addPetModalShown = false" />
              </div>

              <div class="p-5">
                <!-- pet categories -->
                <dropdown
                  v-model="form.pet_category_id"
                  :data="data.pet_categories"
                  :required="true"
                  :placeholder="'Choose a value'"
                  :dropdown-class="'block w-full'"
                  :label="'Pet category'" />
              </div>
            </div>

            <div class="flex justify-between p-5">
              <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="editedPetId = 0" />
              <pretty-button :text="'Save'" :state="loadingState" :icon="'check'" :classes="'save'" />
            </div>
          </form>
        </li>
      </ul>
    </div>

    <!-- blank state -->
    <div v-if="localPets.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no pets yet.</p>
    </div>
  </div>
</template>

<script>
import HoverMenu from '@/Shared/HoverMenu';
import PrettyButton from '@/Shared/Form/PrettyButton';
import PrettySpan from '@/Shared/Form/PrettySpan';
import TextInput from '@/Shared/Form/TextInput';
import Dropdown from '@/Shared/Form/Dropdown';
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    HoverMenu,
    PrettyButton,
    PrettySpan,
    TextInput,
    Dropdown,
    Errors,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
    paginator: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      loadingState: '',
      addPetModalShown: false,
      localPets: [],
      editedPetId: 0,
      form: {
        name: '',
        pet_category_id: 0,
        errors: [],
      },
    };
  },

  created() {
    this.localPets = this.data.pets;
  },

  methods: {
    showCreatePetModal() {
      this.addPetModalShown = true;
      this.form.errors = [];
      this.form.name = '';
      this.form.pet_category_id = 0;

      this.$nextTick(() => {
        this.$refs.newName.focus();
      });
    },

    showEditPetModal(pet) {
      this.form.errors = [];
      this.editedPetId = pet.id;
      this.form.pet_category_id = pet.pet_category.id;
      this.form.name = pet.name;
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.flash('The pet has been added', 'success');
          this.localPets.unshift(response.data.data);
          this.loadingState = '';
          this.addPetModalShown = false;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    update(reminder) {
      this.loadingState = 'loading';

      axios
        .put(reminder.url.update, this.form)
        .then((response) => {
          this.loadingState = '';
          this.flash('The pet has been edited', 'success');
          this.localPets[this.localPets.findIndex((x) => x.id === reminder.id)] = response.data.data;
          this.editedPetId = 0;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    destroy(reminder) {
      if (confirm('Are you sure? This will delete the pet permanently.')) {
        axios
          .delete(reminder.url.destroy)
          .then((response) => {
            this.flash('The pet has been deleted', 'success');
            var id = this.localPets.findIndex((x) => x.id === reminder.id);
            this.localPets.splice(id, 1);
          })
          .catch((error) => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>
