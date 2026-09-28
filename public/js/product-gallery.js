(() => {
  const SWIPE_THRESHOLD = 50;

  function initGallery(gallery) {
    const slides = [...gallery.querySelectorAll(".product-gallery-slide")];
    const previousButton = gallery.querySelector(
      ".product-gallery-control--previous",
    );
    const nextButton = gallery.querySelector(".product-gallery-control--next");
    const count = gallery.querySelector(".product-gallery-count");
    const thumbnails = gallery.querySelector(".product-gallery-thumbnails");

    if (!slides.length) return;

    let activeIndex = slides.findIndex((slide) =>
      slide.classList.contains("is-active"),
    );
    activeIndex = activeIndex === -1 ? 0 : activeIndex;
    gallery.dataset.slideCount = slides.length;

    slides.forEach((slide, index) => {
      slide.setAttribute(
        "aria-hidden",
        index === activeIndex ? "false" : "true",
      );
      const image = slide.querySelector("img");
      if (!image || !thumbnails) return;

      const thumbnail = document.createElement("button");
      thumbnail.type = "button";
      thumbnail.className = "product-gallery-thumbnail";
      thumbnail.setAttribute("aria-label", `Show product image ${index + 1}`);
      const thumbnailImage = document.createElement("img");
      thumbnailImage.src = image.currentSrc || image.src;
      thumbnailImage.alt = "";
      thumbnailImage.loading = "lazy";
      thumbnailImage.decoding = "async";
      thumbnail.appendChild(thumbnailImage);
      thumbnail.addEventListener("click", () => showSlide(index));
      thumbnails.appendChild(thumbnail);
    });

    const thumbnailButtons = thumbnails ? [...thumbnails.children] : [];

    function showSlide(nextIndex) {
      activeIndex = (nextIndex + slides.length) % slides.length;
      slides.forEach((slide, index) => {
        const isActive = index === activeIndex;
        slide.classList.toggle("is-active", isActive);
        slide.setAttribute("aria-hidden", String(!isActive));
      });
      thumbnailButtons.forEach((thumbnail, index) => {
        const isActive = index === activeIndex;
        thumbnail.classList.toggle("is-active", isActive);
        thumbnail.setAttribute("aria-current", isActive ? "true" : "false");
      });
      if (count) count.textContent = `${activeIndex + 1} / ${slides.length}`;
    }

    previousButton?.addEventListener("click", () => showSlide(activeIndex - 1));
    nextButton?.addEventListener("click", () => showSlide(activeIndex + 1));

    gallery.addEventListener("keydown", (event) => {
      if (event.key === "ArrowLeft") showSlide(activeIndex - 1);
      if (event.key === "ArrowRight") showSlide(activeIndex + 1);
    });

    let startX = 0;
    gallery.addEventListener("pointerdown", (event) => {
      startX = event.clientX;
    });
    gallery.addEventListener("pointerup", (event) => {
      const distance = event.clientX - startX;
      if (Math.abs(distance) < SWIPE_THRESHOLD) return;
      showSlide(activeIndex + (distance < 0 ? 1 : -1));
    });

    showSlide(activeIndex);
  }

  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-product-gallery]").forEach(initGallery);
  });
})();
