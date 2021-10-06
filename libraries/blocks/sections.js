class blocks {
  constructor(blockManager) {
    this.blockManager = blockManager;
    this.categoryId = "section-service";
    this.categoryLabel = "Models services";
  }

  loadBlocks() {
    this.Service1();
  }

  Service1() {
    import("../componentHtml/SectionServices/Service1.html").then((data) => {
      this.blockManager.add("service1", {
        label: "Service1",
        content: data.default,
        media: '<img src="/imgs/section1.png" />',
        category: {
          id: this.categoryId,
          label: this.categoryLabel,
          open: false,
        },
      });
    });
  }
}
export default blocks;
