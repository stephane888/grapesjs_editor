import grapesjs from "grapesjs";
import "grapesjs-preset-webpage";
import "grapesjs-bootstrap4-blocks";
import "../scss/style.scss";
import code_editor from "grapesjs-component-code-editor";
import parserPostCSS from "grapesjs-parser-postcss";
import blocksSections from "../blocks/sections";
import blocksTeasers from "../blocks/blocksTeasers";

window.grapesjs = grapesjs;
(function ($, Drupal, grapesjs) {
  Drupal.editors.grapesjs_editor = {
    editors: {},
    getFieldName(element) {
      return $(element).attr("name").split("[")[0];
    },
    attach(element, format) {
      /* Rebuild body field */
      const fieldName = this.getFieldName(element);
      const gjsContainer = $("<div/>", {
        id: `gjs-container-${fieldName}`,
        class: "gjs",
        "data-field-name": $(element).attr("name"),
      });
      $(element).parent().prepend(gjsContainer);
      $(element).hide();
      const grapesSettings = {
        // Indicate where to init the editor. You can also pass an HTMLElement
        container: gjsContainer.get(0),
        components: $(element).val(),
        plugins: [],
        ...format.editorSettings.grapesSettings,
      };

      /* Add body field element to plugin options */
      grapesSettings.plugins.forEach((name, plugin) => {
        if (typeof grapesSettings.pluginsOpts[name] === "undefined") {
          grapesSettings.pluginsOpts[name] = {};
        }
        grapesSettings.pluginsOpts[name].element = element;
      });
      /* *** */
      grapesSettings.plugins.push("gjs-preset-webpage");
      grapesSettings.plugins.push("grapesjs-blocks-bootstrap4");
      grapesSettings.plugins.push(code_editor);
      grapesSettings.plugins.push(parserPostCSS);
      //
      grapesSettings.pluginsOpts["gjs-preset-webpage"] = {
        formsOpts: false,
        showStylesOnChange: false,
        // exportOpts: false,
      };
      grapesSettings.canvas = {
        scripts: [
          "https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js",
          "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js",
          "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js",
        ],
        styles: [
          "/themes/custom/lesroisdelareno/modele/themes/custom/gater/css/fonts/picons.css",
          "/themes/custom/lesroisdelareno/modele/themes/custom/gater/css/fonts/fontello.css",
          "/themes/custom/lesroisdelareno/css/prestataires-m1-default.css",
          "/themes/custom/lesroisdelareno/css/prestataires-m1.css?" +
            Math.random(),
        ],
      };
      //
      this.editors[fieldName] = grapesjs.init(grapesSettings);
      /**
       * -
       */
      const locale = format.editorSettings.currentLanguage;
      if (locale !== "en") {
        import(/* webpackMode: "eager" */ `grapesjs/src/i18n/locale/${locale}`)
          .then((module) => {
            const messages = { [locale]: module.default };
            this.editors[fieldName].I18n.setLocale(locale);
            this.editors[fieldName].I18n.addMessages(messages);
          })
          .catch((err) => {
            console.error(`Locale "${locale}" not found.`);
          });
      }
      this.editors[fieldName].on("load", () => {
        /* Disable Drupal form submit */
        $("input", gjsContainer).on("keydown", function (e) {
          if (e.keyCode === 13) {
            e.preventDefault();
          }
        });
        /**
         * close all categories
         */
        var blockManager = this.editors[fieldName].BlockManager;
        const Categories = blockManager.getCategories().models;
        //console.log("Categories", Categories);
        Categories.map((category) => {
          category.set("open", false);
          //console.log(category);
        });
        // ajout d'un button;
        const pn = this.editors[fieldName].Panels;
        const panelViews = pn.addPanel({
          id: "views",
        });
        panelViews.get("buttons").add([
          {
            attributes: {
              title: "Open Code",
            },
            className: "fa fa-file-code-o",
            command: "open-code",
            togglable: false, //do not close when button is clicked again
            id: "open-code",
          },
        ]);
        // Ajout des blocks
        var blockManager = this.editors[fieldName].BlockManager;
        const blockSection = new blocksSections(blockManager);
        blockSection.loadBlocks();
        const blockTeaser = new blocksTeasers(blockManager);
        blockTeaser.loadBlocks();
        // import("../componentHtml/SectionServices/Service1.html").then(
        //   (data) => {
        //     blockManager.add("my-first-block", {
        //       label: "Service 1",
        //       content: data.default,
        //       media: '<img src="/imgs/section1.png" />',
        //       category: {
        //         id: "section-service",
        //         label: "Models services",
        //       },
        //     });
        //   }
        // );
        /**
         * Desactiver le click sur le button code-edit ;
         */
        this.editors[fieldName].on("run:open-code", () => {
          var button = document.querySelector(".cp-apply-html");
          if (button.tagName == "BUTTON") {
            // var span = document.createElement("span");
            // span.innerHTML = button.innerHTML;
            // //console.log("getAttributeNames : ", button.getAttributeNames());
            // button.getAttributeNames().forEach((attribute) => {
            //   //console.log("attribute : ", button.getAttribute(attribute));
            //   span.setAttribute(attribute, button.getAttribute(attribute));
            // });
            // button.parentNode.replaceChild(span, button);
            //console.log("button.parentNode : ", button.parentNode);
            button.addEventListener("click", function (event) {
              event.preventDefault();
            });
          }

          var buttonCss = document.querySelector(".cp-apply-css");
          if (buttonCss.tagName == "BUTTON") {
            buttonCss.addEventListener("click", function (event) {
              event.preventDefault();
            });
          }
        });
      });
    },
    detach(element) {
      const fieldName = this.getFieldName(element);
      const gjsContainer = $(`#gjs-container-${fieldName}`);
      $(element).show();
      this.editors[fieldName].destroy();
      gjsContainer.remove();
    },
    onChange() {},
  };
})(jQuery, Drupal, grapesjs);

// (function ($, Drupal, once) {
//   Drupal.behaviors.myModuleBehavior = {
//     attach: function (context, settings) {
//       once("myCustomBehavior", "input.myCustomBehavior", context).forEach(
//         function () {
//           // Apply the myCustomBehaviour effect to the elements only once.
//         }
//       );
//     },
//   };
// })(jQuery, Drupal, once);
