<template>
  <div>
    <!-- Knap der åbner billedvælger-modal -->
    <v-btn color="primary" @click="openSelectModal">Vælg billede...</v-btn>

    <!-- Viser de valgte billeder -->
    <div class="selected-thumbs">
      <draggable v-model="selectedImages" class="thumb-list" :options="{ animation: 200 }" @end="updateModel">
        <div v-for="(image, index) in selectedImages" :key="image.id" class="thumb-wrapper">
          <v-img :src="getLink(image)" width="40" height="40" @click="openPreview(image)"></v-img>
          <v-btn icon small class="remove-btn" @click.stop="removeImage(index)">
            <v-icon small>mdi-close</v-icon>
          </v-btn>
        </div>
      </draggable>
    </div>

    <!-- Billedvælger Modal -->
    <v-dialog v-model="selectModal" max-width="1000px">
      <v-card>
        <v-card-title>
          Vælg billeder
          <v-spacer></v-spacer>
          <!-- Trigger til filvalg (bruges også af drop-zone) -->
          <v-btn color="primary" @click="triggerUpload">Upload billede</v-btn>
          <input type="file" ref="uploadInput" multiple accept="image/*" style="display: none" @change="handleFileSelect" />
        </v-card-title>
        <v-card-text>

         

          <!-- Drop Zone -->
          <div class="drop-zone mb-4" @dragover.prevent @drop.prevent="handleDrop" @click="triggerUpload">
            <p v-if="availableImages.length === 0">
              Træk billeder her eller klik for at vælge filer. Der er ikke flere billeder.
            </p>
            <p v-else>
              Træk flere billeder her for at uploade.
            </p>
          </div>

          <!-- Uploader Progress Bar -->
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

          <Loading v-if="loading"/>

          <!-- Gitter med tilgængelige billeder -->
          <v-container fluid v-else>
            <v-row>
              <div v-for="image in availableImages" :key="image.id" cols="3">
                <v-card
                  class="pa-2 selectable-image ml-1 mr-1"
                  :class="{ 'selected-image': selectedImages.some(img => img.id === image.id) }"
                  @click="toggleSelect(image)"
                >
                  <v-img :src="getLink(image)" width="80" height="80" contain></v-img>
                  <!-- Vis-knap under billedet, stopper klik-propagation så det ikke vælger billedet igen -->
                  <v-btn small text color="primary" @click.stop="openPreview(image)">
                    <span class="mdi mdi-eye-outline"></span>
                    </v-btn>
                  <v-icon
                    v-if="selectedImages.some(img => img.id === image.id)"
                    class="selected-check-icon"
                    color="blue"
                    small
                  >
                    mdi-check-circle
                  </v-icon>
                </v-card>
              </div>
            </v-row>
          </v-container>
          
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
    
          <v-btn text color="secondary" @click="selectModal = false">Luk</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Forhåndsvisningsmodal -->
    <v-dialog v-model="previewModal" max-width="600px" @click:outside="previewModal = false">
      <v-card>
        <v-card-title>Forhåndsvisning</v-card-title>
        <v-card-text>
          <v-img :src="getLink(previewImage)" aspect-ratio="1"></v-img>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text color="primary" @click="previewModal = false">Luk</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import draggable from 'vuedraggable';
import collect from 'collect.js';
import axios from 'axios';

export default {
  name: "ImageSelect",
  components: { draggable },
  props: {
    // v-model forventes at modtage en array af billed-ID'er
    value: {
      default: () => []
    },
    single: {
      default: true
    }
  },
  data() {
    return {
      selectModal: false,
      previewModal: false,
      previewImage: {},
      selectedImages: [], // Gemmer hele billedobjekter internt
      availableImages: [],
      loading: false,
      isUploading: false,
      totalFiles: 0,
      uploadedCount: 0
    };
  },
  computed: {
    progressPercentage() {
      return this.totalFiles ? (this.uploadedCount / this.totalFiles) * 100 : 0;
    }
  },
  watch: {
    // Når selectedImages ændres, emitteres kun ID'erne
    selectedImages: {
      handler() {
        this.returnImages();
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    openSelectModal() {
      this.selectModal = true;
      this.fetchImages();
    },
    fetchImages() {
      this.loading = true;
      axios
        .post(route("api.media.images.search"), {
          filters: [{ field: "collection_name", operator: "=", value: "media" }]
        })
        .then(response => {
          this.availableImages = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error("Fejl ved hentning af billeder:", error);
          this.loading = false;
        });
    },
    toggleSelect(image) {

      if(this.single){ this.selectedImages = [] }

      const index = this.selectedImages.findIndex(img => img.id === image.id);
      if (index === -1) {
        this.selectedImages.push(image);
      } else {
        this.selectedImages.splice(index, 1);
      }
      // returnImages() kaldes også via watch
    },
    getLink(image) {
      return image.file_name ? route("media.show", { filename: image.file_name }) : '';
    },
    saveSelection() {
      this.returnImages();
      this.selectModal = false;
    },
    openPreview(image) {
      this.previewImage = image;
      this.previewModal = true;
    },
    removeImage(index) {
      this.selectedImages.splice(index, 1);
      // returnImages() kaldes via watch
    },
    updateModel() {
      // Efter drag & drop opdateres modellen
      // returnImages() kaldes via watch
    },
    returnImages() {
      
      let imageIds = this.selectedImages.map(image => image.id);

      if(this.single){ imageIds = imageIds[0]  }
        

      this.$emit("input", imageIds);
    },
    // --- Filupload med drop-zone ---
    triggerUpload() {
      this.$refs.uploadInput.click();
    },
    handleFileSelect(e) {
      const files = e.target.files;
      this.uploadFiles(files);
    },
    handleDrop(e) {
      const files = e.dataTransfer.files;
      this.uploadFiles(files);
    },
    uploadFiles(files) {
      if (!files.length) return;
      this.isUploading = true;
      this.totalFiles = files.length;
      this.uploadedCount = 0;
      Array.from(files).forEach(file => {
        let formData = new FormData();
        formData.append('file', file);
        axios
          .post(route("api.media.images.store", { collection: "media" }), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: progressEvent => {
              // Eventuelt kan du beregne filspecifik progress her
            }
          })
          .then(response => {
            this.availableImages.push(response.data);
            this.uploadedCount++;
            if (this.uploadedCount === this.totalFiles) {
              this.isUploading = false;
            }
          })
          .catch(error => {
            console.error("Upload fejl:", error);
            this.isUploading = false;
          });
      });
    }
  },
  mounted() {
    // Ved mount omdannes den modtagne v-model (array af ID'er) ikke til billedobjekter,
    // medmindre availableImages allerede er loadet – hvilket kan ske senere via fetchImages().
    // Derfor kan du evt. lytte efter en ændring i availableImages for at matche dem.
    // Her sætter vi selectedImages til en tom liste, men du kan implementere en logik, der matcher ID'er med objekter.
   // this.selectedImages = [];
  }
};
</script>

<style scoped>
.thumb-list {
  display: flex;
  gap: 8px;
  margin-top: 8px;
}
.thumb-wrapper {
  position: relative;
}
.remove-btn {
  position: absolute;
  top: -4px;
  right: -4px;
  background: white;
  border-radius: 50%;
}
.selected-image {
  border: 2px solid blue;
}
.selected-check-icon {
  position: absolute;
  top: 5px;
  right: 5px;
  background: white;
  border-radius: 50%;
}
.selectable-image {
  cursor: pointer;
  transition: box-shadow 0.3s;
}
.selectable-image:hover {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}
.selected-thumbs {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}
.drop-zone {
  border: 2px dashed #ccc;
  padding: 20px;
  text-align: center;
  cursor: pointer;
}
</style>
