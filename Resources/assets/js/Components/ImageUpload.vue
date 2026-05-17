<template>
  <section>
    <!-- Collection Dropdown -->
    <v-select
      v-model="selectedCollection"
      :items="collections"
      label="Vælg Collection"
      outlined
      class="mb-4"
    ></v-select>

    <!-- Folder Selector -->
    <folder-selector
      :folders="folders"
      @folderSelected="handleFolderSelect"
      @moveImage="handleMoveImage"
      @folderCreated="refreshFolders"
    ></folder-selector>

    <!-- Drop Zone -->
    <div
      class="drop-zone mb-4"
      @dragover.prevent
      @drop.prevent="handleDrop"
      @click="triggerFileSelect"
    >
      <p>Træk billeder her eller klik for at vælge filer</p>
      <input
        type="file"
        ref="fileInput"
        multiple
        accept="image/*"
        @change="handleFileSelect"
        style="display: none;"
      />
    </div>

    <!-- Upload Progress -->
    <v-progress-linear
      v-if="isUploading"
      :value="progressPercentage"
      height="10"
      striped
      color="blue"
      class="mb-4"
    >
      <template v-slot:default>
        Uploader billeder {{ uploadedCount }}/{{ totalFiles }}
      </template>
    </v-progress-linear>

    <!-- Billedliste -->
    <v-card-text>
      <div class="image-list">


        <Loading v-if="loading"/>

        <div v-else>

          <div
            v-for="image in images"
            :key="image.id"
            class="image-container"
            draggable="true"
            @dragstart="handleDragStart($event, image)"
            @click="openPreview(image)"
          >
            <v-img :src="image.thumb" width="100" height="100"></v-img>
            <v-btn icon small @click.stop="deleteImage(image)">
              <v-icon color="red">mdi-delete</v-icon>
            </v-btn>
          </div>


        </div>

        
      </div>
    </v-card-text>

    <!-- Billed Preview Modal -->
    <v-dialog v-model="previewModal" max-width="500">
      <v-card>
        <v-img :src="currentPreview.image" aspect-ratio="1"></v-img>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="previewModal = false">Luk</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </section>
</template>

<script>
import axios from 'axios'
import FolderSelector from './FolderSelector.vue'

export default {
  components: { FolderSelector },
  data() {
    return {
      collections: [],
      selectedCollection: null,
      folders: [],
      selectedFolder: null,
      images: [],
      isUploading: false,
      totalFiles: 0,
      uploadedCount: 0,
      previewModal: false,
      currentPreview: {},
      
    }
  },
  computed: {
    progressPercentage() {
      return (this.uploadedCount / this.totalFiles) * 100
    }
  },
  methods: {
    fetchCollections() {
      axios.get('/api/collections').then(response => {
        this.collections = response.data
      })
    },
    fetchFolders() {
      axios.get('/api/folders').then(response => {
        this.folders = response.data
      })
    },
    fetchImages() {
      axios.get(route("api.media.media.search")).then(response => {
        this.images = response.data
      })
    },
    triggerFileSelect() {
      this.$refs.fileInput.click()
    },
    handleFileSelect(e) {
      const files = e.target.files
      this.uploadFiles(files)
    },
    handleDrop(e) {
      const files = e.dataTransfer.files
      this.uploadFiles(files)
    },
    uploadFiles(files) {
      if (!files.length) return
      this.isUploading = true
      this.totalFiles = files.length
      this.uploadedCount = 0

      Array.from(files).forEach(file => {
        let formData = new FormData()
        formData.append('image', file)
        formData.append('collection_id', this.selectedCollection)
        if (this.selectedFolder) {
          formData.append('folder_id', this.selectedFolder.id)
        }

        axios
          .post(route("api.media.upload"), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: progressEvent => {
              // Her kan du udregne procent pr. fil, hvis nødvendigt.
            }
          })
          .then(response => {
            this.images.push(response.data)
            this.uploadedCount++
            if (this.uploadedCount === this.totalFiles) {
              this.isUploading = false
            }
          })
          .catch(error => {
            console.error('Upload fejl:', error)
            this.isUploading = false
          })
      })
    },
    openPreview(image) {
      this.currentPreview = image
      this.previewModal = true
    },
    deleteImage(image) {
      axios
        .delete(route("api.media.media.delete",image.id))
        .then(() => {
          this.images = this.images.filter(img => img.id !== image.id)
        })
        .catch(error => console.error('Slet fejl:', error))
    },
    handleDragStart(event, image) {
      event.dataTransfer.setData('image_id', image.id)
    },
    handleMoveImage({ imageId, folder }) {
      const image = this.images.find(img => img.id == imageId)
      if (image) {
        this.moveImageToFolder(image, folder)
      }
    },
    moveImageToFolder(image, folder) {
      axios
        .post(route("api.media.move"), {
          image_id: image.id,
          folder_id: folder.id
        })
        .then(response => {
          // Her kan du fx opdatere image.folder_id eller give feedback til brugeren.
          image.folder_id = folder.id
        })
        .catch(error => console.error('Flytning til mappe fejlede:', error))
    },
    handleFolderSelect(folder) {
      this.selectedFolder = folder
    },
    refreshFolders(newFolder) {
      // Efter oprettelse af en ny mappe kan du f.eks. genindlæse folder-strukturen
      this.fetchFolders()
    }
  },
  mounted() {
    this.fetchCollections()
    this.fetchFolders()
    this.fetchImages()
  }
}
</script>

<style scoped>
.drop-zone {
  border: 2px dashed #ccc;
  padding: 20px;
  text-align: center;
  cursor: pointer;
}
.image-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.image-container {
  position: relative;
}
.image-container v-btn {
  position: absolute;
  top: 5px;
  right: 5px;
}
</style>
