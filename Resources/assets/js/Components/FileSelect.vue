<template>
  <div>
    <pre>{{ selectedFiles }}</pre>

    <!-- Knap der åbner billedvælger-modal -->
    <v-btn color="primary" @click="openSelectModal">Vælg fil...</v-btn>

    <!-- Viser de valgte billeder -->
    <div class="selected-thumbs">
      <div v-for="(selectedFile, index) in selectedFiles" :key="index">
        <div v-if="selectedFile" class="thumb-wrapper">
          <!-- Hvis filen er et billede -->
          <div @click="openPreview(selectedFile)">
            <v-img 
              :src="getImageLink(selectedFile)" 
              width="40" 
              height="40"
              @click="openPreview(selectedFile)"
            ></v-img>
          </div>
        </div>
      </div>
    </div>

    <!-- Billedvælger Modal -->
    <v-dialog v-model="selectModal" max-width="1000px">
      <v-card>
        <v-card-title>
          Vælg filer
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="triggerUpload">Upload fil</v-btn>
          <input type="file" ref="uploadInput" multiple accept="image/*,application/pdf" style="display: none" @change="handleFileSelect" />
        </v-card-title>
        <v-card-text>
          <!-- Drop Zone -->
          <div class="drop-zone mb-4" @dragover.prevent @drop.prevent="handleDrop" @click="triggerUpload">
            <p v-if="availableFiles.length === 0">Drop filer her eller klik for at vælge filer.</p>
            <p v-else>Træk flere filer her for at uploade.</p>
          </div>

          <!-- Uploader Progress Bar -->
          <v-progress-linear v-if="isUploading" :value="progressPercentage" height="10" striped color="blue" class="mb-4">
            <template v-slot:default>
              Uploader filer {{ uploadedCount }}/{{ totalFiles }}
            </template>
          </v-progress-linear>

          <Loading v-if="loading"/>

          <!-- Gitter med tilgængelige filer -->
          <v-container fluid v-else>
            <v-row>
              <div v-for="file in availableFiles" :key="file.id" cols="3">
                <v-card class="pa-2 selectable-image ml-1 mr-1" 
                  :class="{ 'selected-file': selectedFiles.find(f => f.id === file.id) }" 
                  @click="toggleSelect(file)"
                >
                  <!-- Hvis filen er et billede -->
                  <div v-if="file.isImage">
                    <v-img :src="getImageLink(file)" width="80" height="80" contain></v-img>
                  </div>
                  
                  <!-- Hvis filen er en PDF -->
                  <div v-else-if="file.isPDF">
                    <v-img 
                      :src="getStandarsIcon('pdf-icon.png')" 
                      width="80" 
                      height="80" 
                      contain 
                      class="d-flex align-center justify-center"
                    ></v-img>
                  </div>
                  
                  <v-btn small text color="primary" @click.stop="openPreview(file)">
                    Vis
                  </v-btn>
                  <v-icon v-if="selectedFiles.find(f => f.id === file.id)" class="selected-check-icon" color="blue" small>mdi-check-circle</v-icon>
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
  </div>
</template>

<script>
import draggable from 'vuedraggable';
import collect from 'collect.js';
import axios from 'axios';

export default {
  name: "FileSelect",
  components: { draggable },
  props: {
    value: {
      // Vi modtager et array med id'er fra parent
      type: Array,
      default: () => []
    },
    collection: {
      default: "media"
    },
    single: {
      default: false
    }
  },

  data() {
    return {
      selectModal: false,
      previewModal: false,
      previewFile: {},
      selectedFiles: [], // Holder de fulde file-objekter
      availableFiles: [], // Tilgængelige filer til valg
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
    // Når selectedFiles ændres, emitter vi kun en liste med id'er til parent
    selectedFiles: {
      handler() {
        this.returnFiles();
      },
      deep: true,
    },
    // Når availableFiles opdateres, så matcher vi eventuelle id'er fra value med de fulde objekter
    availableFiles(newFiles) {
      if (this.value.length && this.selectedFiles.length === 0) {
        this.selectedFiles = newFiles.filter(file => this.value.includes(file.id));
      }
    }
  },

  methods: {
    openSelectModal() {
      this.selectModal = true;
      this.fetchFiles();
    },

    fetchFiles() {
      this.loading = true;
      axios
        .post(route("api.media.files.search"), {
          filters: [{ field: "collection_name", operator: "=", value: this.collection }]
        })
        .then(response => {
          this.availableFiles = response.data.data.map(file => {
            // Tilføj properties for at skelne mellem billeder og PDF'er
            file.isImage = file.mime_type.startsWith('image/');
            file.isPDF = file.mime_type === 'application/pdf';
            return file;
          });
          this.loading = false;
        })
        .catch(error => {
          console.error("Fejl ved hentning af filer:", error);
          this.loading = false;
        });
    },

    toggleSelect(file) {
      // Find om filen allerede findes i selectedFiles (sammenlign med id)
      const index = this.selectedFiles.findIndex(f => f.id === file.id);
      if (index !== -1) {
        // Fjern filen hvis den findes
        this.selectedFiles.splice(index, 1);
      } else {
        // Tilføj filen
        this.selectedFiles.push(file);
      }
    },

    getLink(file) {
      return file.file_name ? route("media.show", { filename: file.file_name }) : '';
    },

    getImageLink(file) {
      // Udtræk filtypen fra filnavnet og hent standardikonet
      const fileType = file.file_name.split('.').pop().toLowerCase();
      const iconPath = this.getStandarsIcon(`${fileType}-icon.png`);
      return iconPath;
    },

    saveSelection() {
      this.returnFiles();
      this.selectModal = false;
    },

    // Emitter kun et array med id'er til parent
    returnFiles() {
      const ids = this.selectedFiles.map(file => file.id);
      this.$emit("input", ids);
    },

    getStandarsIcon(icon) {
      return "/Modules/Media/images/" + icon;
    },

    openPreview(file) {
      const url = file.src;
      const windowName = "filePreviewWindow"; 
      window.open(url, windowName);
    },

    removeFile() {
      this.selectedFiles = [];
      // returnFiles() bliver kaldt via watch
    },

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
          .post(route("api.media.files.store", { collection: "media" }), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: progressEvent => {
              // Håndter evt. filspecifik progress her
            }
          })
          .then(response => {
            this.availableFiles.push(response.data);
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
    // Start med en tom liste - eventuelle id'er fra value matches, når availableFiles hentes
    this.selectedFiles = [];
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
.selectable-file {
  cursor: pointer;
  transition: box-shadow 0.3s;
}
.selectable-file:hover {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}
.pdf-card {
  border: 2px dashed #ccc;
  padding: 10px;
  text-align: center;
  cursor: pointer;
}
.pdf-label {
  font-size: 14px;
  font-weight: bold;
  width: 80px;
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
