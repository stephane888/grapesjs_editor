class blocks {
  constructor(blockManager) {
    this.blockManager = blockManager;
    this.categoryId = "teasers-service";
    this.categoryLabel = "Teasers";
  }

  loadBlocks() {
    this.teaser1();
  }

  teaser1() {
    import("../componentHtml/Teasers/block-icon-titre-desc.html").then(
      (data) => {
        this.blockManager.add("block-icon-titre-desc", {
          label: "block-icon-titre-desc",
          content: data.default,
          media: '<img src="/imgs/section1.png" />',
          category: {
            id: this.categoryId,
            label: this.categoryLabel,
            open: false,
          },
        });
      }
    );
  }
}
export default blocks;
