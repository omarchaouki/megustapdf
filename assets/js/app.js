// app.js
new Vue({
  el: '#app',
  data: {
    file: null,
    pdfLink: null,
    errorMessage: null,
  },
  methods: {
    onFileChange(event) {
      // Update this.file with the selected file
      this.file = event.target.files[0];
    },
    
    convertToPDF() {
      const formData = new FormData();
      formData.append('docxFile', this.file);

      axios
        .post('./convert.php', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
        .then((response) => {
          // Handle the response from your PHP script
          if (response.data.success) {
            this.pdfLink = response.data.pdfLink;
          } else {
            this.errorMessage = response.data.errorMessage;
          }
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    },
  },
});
