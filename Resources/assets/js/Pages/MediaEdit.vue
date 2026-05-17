<template>
  <section>

    <Loading v-if="loading"/>
    <div v-else>

        <div v-if="imageUrl">
        <ImageEditor :imageUrl="imageUrl" />
        </div>
        <div v-else>
        <p>Billedet blev ikke fundet.</p>
        </div>

    </div>

    
  </section>
</template>

<script>
import axios from "axios";
import ImageEditor from "./../Components/ImageEditor.vue";

export default {
  name: "MyPage",
  components: {
    ImageEditor,
  },
  data() {
    return {
      imageUrl: null,
      loading: true,
      id: this.$route.query.id
    };
  },
  mounted() {
    this.fetchImage();
  },
  methods: {
    fetchImage() {
  
      axios
        .get(route("api.media.images.show",{ id: 26 })) // API-kald til at hente billede
        .then((response) => {
          // Hvis billede findes, tildel URL'en
          this.imageUrl = response.data.data.src; // Skift til korrekt data fra API'en
          this.loading = false
        })
        .catch((error) => {
          console.error("Fejl ved hentning af billede:", error);
        });
    },
  },
};
</script>

<style scoped>
/* Tilføj eventuel styling her */
</style>
