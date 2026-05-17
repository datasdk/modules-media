<template>
  <section>

    <Loading v-if="loading"/>

    <div v-else>

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

          <div v-for="image in images" :key="image.id">

            <div 
              class="image-container"
              draggable="true"
              @dragstart="handleDragStart($event, image)"
              @click="openPreview(image)"
            >

              <span v-if="isImage(image)">

                <v-img :src="image.src" width="100" height="100"></v-img>

              </span>
              <span v-else >
              
                  {{ image.file_type }}

              </span>

              
              <v-btn icon small @click.stop="deleteImage(image)">
                X
              </v-btn>

            </div>

          </div>

        </div>


      </v-card-text>

      <!-- Billed Preview Modal -->
      <v-dialog v-model="previewModal" max-width="500" @click:outside="closeDialog">
        <v-card>
        <v-card-title>Image preview</v-card-title>
        
          <v-img :src="currentPreview?.src" aspect-ratio="1"></v-img>
          <v-card-actions>

    

             <div class="p-2">
              <div><small>Filename: <a :href="currentPreview.src" target="blank">{{currentPreview.file_name}}</a></small></div>
              <div><small>Collection: {{currentPreview.collection_name}}</small></div>
              <div><small>Size:{{ formatBytes(currentPreview.size) }}</small></div>
              <div><small><a :href="currentPreview.download_link" >Download fil</a></small></div>
            </div>
            
            <v-spacer></v-spacer>
            <v-btn color="primary" text @click="previewModal = false">Luk</v-btn>
          </v-card-actions>

  
        </v-card>
      </v-dialog>

    </div>
    
  </section>
</template>

<script>

import FolderSelector from './../Components/FolderSelector.vue'

export default {
  components: { FolderSelector },
  data() {
    return {
      loading: true,
      collections: ["media"],
      selectedCollection: "media",
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
      return ["media"]
    },
    fetchFolders() {
      axios.get(route("api.folders.index")).then(response => {

        let data = response.data.data
        this.folders = data
      })
    },
    fetchImages() {
      axios.post(route("api.media.images.search"),{
        filters: [
          { field : "collection_name", "operator" : "=", "value" : this.selectedCollection }
        ]
      }).then(response => {
        this.images = response.data.data
        this.loading = false
      })
    },

    isImage(image){

      return image.file_type === 'image'

    },
    closeDialog(){

      this.previewModal = false

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

    formatBytes(bytes) {

        if (bytes < 1024) {
          return `${bytes} bytes`; // Hvis mindre end 1 KB
        }
        
        const kb = bytes / 1024; // Konverter bytes til KB
        if (kb < 1024) {
          return `${Math.floor(kb)} KB`; // Hvis mindre end 1 MB, afrunde til hele KB
        }

        const mb = kb / 1024; // Konverter KB til MB
        if (mb < 1024) {
          return `${Math.floor(mb)} MB`; // Hvis mindre end 1 GB, afrunde til hele MB
        }

        const gb = mb / 1024; // Konverter MB til GB
        return `${Math.floor(gb)} GB`; // Hvis større end 1 GB, afrunde til hele GB

    },

    uploadFiles(files) {
      if (!files.length) return
      this.isUploading = true
      this.totalFiles = files.length
      this.uploadedCount = 0

      Array.from(files).forEach(file => {
        let formData = new FormData()
        formData.append('file', file)
        formData.append('collection_id', this.selectedCollection)
        
        if (this.selectedFolder) {
          formData.append('folder_id', this.selectedFolder.id)
        }

        axios
          .post(route("api.media.images.store",{ collection: this.selectedCollection }), formData, {
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

      if(this.isImage(image)){

        this.currentPreview = image
        this.previewModal = true

      } else {

        window.open(image.src);

      }


      
    },
    deleteImage(image) {
      axios
        .delete(route("api.media.images.delete",{ id: image.id}))
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
        .post(route("api.folders.move",{ 
          image_id: image.id,
          folder_id: folder.id
        }))
        .then(response => {
          // Her kan du fx opdatere image.folder_id eller give feedback til brugeren.
          image.folder_id = folder.id
        })
        .catch(error => console.error('Flytning til mappe fejlede:', error))
    },
    handleFolderSelect(folder) {
      console.log(folder)
      this.selectedFolder = folder
    },
    refreshFolders(newFolder) {
      // Efter oprettelse af en ny mappe kan du f.eks. genindlæse folder-strukturen
      this.fetchFolders()
    }
  },
  mounted() {

    
    this.fetchImages()
    this.fetchFolders()
    
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
