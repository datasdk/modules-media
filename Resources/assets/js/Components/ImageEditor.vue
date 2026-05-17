<template>
  <div>
    <!-- Modal til billedredigering -->
  
      <v-card>
        <v-card-title>
          Rediger billede
        </v-card-title>
        <v-card-text>
          <!-- Canvas hvor billedet vises -->
          <canvas ref="canvas" id="editor-canvas" width="600" height="400"></canvas>

          <!-- Kontroller: Slidere til billedjustering -->
          <v-slider
            v-model="brightness"
            :min="-100"
            :max="100"
            label="Lysstyrke"
            class="mb-3"
            @input="applyFilters"
          ></v-slider>
          <v-slider
            v-model="contrast"
            :min="-100"
            :max="100"
            label="Kontrast"
            class="mb-3"
            @input="applyFilters"
          ></v-slider>
          <v-slider
            v-model="saturation"
            :min="-100"
            :max="100"
            label="Mætning"
            class="mb-3"
            @input="applyFilters"
          ></v-slider>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="submitImage">Submit</v-btn>
          <v-btn text @click="closeEditor">Luk</v-btn>
        </v-card-actions>
      </v-card>

  </div>
</template>

<script>
import { fabric } from "fabric";
import axios from "axios";

export default {
  name: "ImageEditor",
  props: {
    // URL til det billede, der skal redigeres
    imageUrl: {
      type: String,
      required: true
    },
    // Boolean der styrer om dialogen vises
    dialog: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      brightness: 0,
      contrast: 0,
      saturation: 0,
      canvas: null,
      imageObject: null,
    };
  },
  watch: {
    // Når dialogen åbnes, initialiseres canvas med billedet
    dialog(newVal) {
      if (newVal) {
        this.initCanvas();
      }
    }
  },
  methods: {
    initCanvas() {
      // Hvis canvas ikke er initialiseret, oprettes det
      if (!this.canvas) {
        this.canvas = new fabric.Canvas(this.$refs.canvas);
      } else {
        this.canvas.clear();
      }
     
      // Hent billedet via fabric og tilføj til canvas
      console.log('Henter billede fra URL:', this.imageUrl); // Fejlfinding

      fabric.Image.fromURL("http://www.nara.ac.lk/wp-content/uploads/2022/12/yellow-orange-starburst-flower-nature-jpg-192959431.jpg", (img) => {
        this.imageObject = img;
        // Skalér billedet til at passe indenfor canvas'et
        img.scaleToWidth(this.canvas.width);
        this.canvas.add(img);
        this.canvas.renderAll();
        // Anvend eventuelle filtre (her 0, da vi netop har hentet billedet)
        console.log('Billede indlæst og tilføjet til canvas:', img); // Fejlfinding
        this.applyFilters();
      }, (error) => {
        // Fejl ved indlæsning af billede
        console.error('Fejl ved indlæsning af billede:', error);
      });
    },
    applyFilters() {
      if (this.imageObject) {
        // Opret filtre med de aktuelle slider-værdier
        console.log('Anvender filtre:', {
          brightness: this.brightness,
          contrast: this.contrast,
          saturation: this.saturation
        }); // Fejlfinding

        this.imageObject.filters = [
          new fabric.Image.filters.Brightness({ brightness: this.brightness / 100 }),
          new fabric.Image.filters.Contrast({ contrast: this.contrast / 100 }),
          new fabric.Image.filters.Saturation({ saturation: this.saturation / 100 }),
        ];
        // Anvend filtre og opdater canvas
        this.imageObject.applyFilters();
        this.canvas.renderAll();
      }
    },
    submitImage() {
      // Konverter canvas-indholdet til en dataURL (PNG)
      const dataURL = this.canvas.toDataURL({ format: "png" });
      // Konverter dataURL til en Blob
      const blob = this.dataURItoBlob(dataURL);
      // Opret FormData til upload
      const formData = new FormData();
      formData.append("image", blob, "edited_image.png");
      // Uploade billedet til serveren via Axios (tilpas URL efter behov)
      axios
        .post("/api/media/upload", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        })
        .then((response) => {
          console.log("Billedet blev uploadet:", response.data);
          // Emit en event, hvis forælderen skal håndtere resultatet
          this.$emit("upload-success", response.data);
          this.dialog = false;
        })
        .catch((error) => {
          console.error("Fejl ved upload af billede:", error);
        });
    },
    // Hjælpefunktion til at konvertere dataURL til en Blob
    dataURItoBlob(dataURI) {
      let byteString;
      if (dataURI.split(",")[0].indexOf("base64") >= 0) {
        byteString = atob(dataURI.split(",")[1]);
      } else {
        byteString = decodeURI(dataURI.split(",")[1]);
      }
      const mimeString = dataURI.split(",")[0].split(":")[1].split(";")[0];
      const ia = new Uint8Array(byteString.length);
      for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
      }
      return new Blob([ia], { type: mimeString });
    },
    closeEditor() {
      this.dialog = false;
    },
  },
};
</script>

<style scoped>
#editor-canvas {
  border: 1px solid #ccc;
  margin-bottom: 16px;
}
</style>
