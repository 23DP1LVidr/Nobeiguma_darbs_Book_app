<template>
  <v-dialog :model-value="modelValue" max-width="520" @update:model-value="emitClose">
    <v-card rounded="xl" class="pa-6 content-card">
      <div class="text-h6 font-weight-bold mb-4">{{ title }}</div>

      <v-text-field v-model="form.title" label="Nosaukums" variant="outlined" class="mb-3" />
      <v-text-field v-model="form.author" label="Autors" variant="outlined" class="mb-3" />

      <v-select v-model="form.genre" :items="genres" label="Žanrs" variant="outlined" class="mb-3" />
      <v-select v-model="form.condition" :items="conditions" label="Stāvoklis" variant="outlined" class="mb-3" />

      <v-file-input
        v-model="form.imageFile"
        label="Grāmatas attēls"
        accept="image/*"
        prepend-icon="mdi-image"
        show-size
        class="mb-3"
      />

      <v-img v-if="imagePreview" :src="imagePreview" height="160" class="rounded mb-3" cover />

      <div class="d-flex justify-end mt-4">
        <v-btn variant="text" class="mr-2" @click="close">
          Atcelt
        </v-btn>

        <v-btn color="primary" :loading="saving" @click="submit">
          Saglabāt
        </v-btn>
      </div>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed, ref, watch } from "vue"

const emptyForm = () => ({
  id: null,
  title: "",
  author: "",
  genre: "",
  condition: "",
  image: "",
  imageFile: null,
})

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  mode: {
    type: String,
    default: "add",
    validator: (value) => ["add", "edit"].includes(value),
  },
  book: {
    type: Object,
    default: null,
  },
  genres: {
    type: Array,
    required: true,
  },
  conditions: {
    type: Array,
    required: true,
  },
  saving: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(["update:modelValue", "save"])

const form = ref(emptyForm())
const imagePreview = ref(null)

const title = computed(() => {
  return props.mode === "edit" ? "Rediģēt grāmatu" : "Pievienot grāmatu"
})

watch(
  () => props.modelValue,
  (isOpen) => {
    if (isOpen) {
      resetForm()
    }
  }
)

watch(
  () => props.book,
  () => {
    if (props.modelValue) {
      resetForm()
    }
  }
)

watch(
  () => form.value.imageFile,
  (file) => {
    if (!file) return

    const selected = Array.isArray(file) ? file[0] : file
    if (!selected) return

    const reader = new FileReader()

    reader.onload = (event) => {
      imagePreview.value = event.target.result
      form.value.image = event.target.result
    }

    reader.readAsDataURL(selected)
  }
)

function resetForm() {
  if (props.mode === "edit" && props.book) {
    form.value = {
      id: props.book.id,
      title: props.book.title || "",
      author: props.book.author || "",
      genre: props.book.genre || "",
      condition: props.book.condition || "",
      image: props.book.image || "",
      imageFile: null,
    }
    imagePreview.value = props.book.image || null
    return
  }

  form.value = emptyForm()
  imagePreview.value = null
}

function emitClose(value) {
  emit("update:modelValue", value)
}

function close() {
  emit("update:modelValue", false)
}

function submit() {
  emit("save", {
    id: form.value.id,
    title: form.value.title,
    author: form.value.author,
    genre: form.value.genre,
    condition: form.value.condition,
    image: form.value.image,
  })
}
</script>

<style scoped>
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}
</style>
