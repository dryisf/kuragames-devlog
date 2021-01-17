/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";
import "./styles/app.scss";

require("bootstrap");

// start the Stimulus application
import "./bootstrap";

const displayContent = (language) => {
  document.querySelector(`.content-${language}`).classList.remove("hidden");
  document
    .querySelector(`.content-${language === "french" ? "english" : "french"}`)
    .classList.add("hidden");
};

window.onload = () => {
  const frenchContentButton = document.querySelector(".display-french");
  const englishContentButton = document.querySelector(".display-english");

  const navToTop = document.querySelector(".navToTop");

  navToTop.onclick = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  if (frenchContentButton)
    frenchContentButton.onclick = () => {
      displayContent("french");
    };

  if (englishContentButton)
    englishContentButton.onclick = () => {
      displayContent("english");
    };
};
