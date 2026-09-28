"use strict";

// Banner slider
let currentSlide = 0;
let autoSlideInterval;
let autoSlideResumeTimeout;
const AUTO_SLIDE_INTERVAL_MS = 5000;

function bannerSlides() {
  return document.querySelectorAll(".banner-slide");
}

function bannerProgressBars() {
  return document.querySelectorAll(".banner-progress-bar");
}

function resetProgressBar(bar) {
  const fill = bar.querySelector(".banner-progress-fill");
  if (fill) {
    fill.style.animation = "none";
    void fill.offsetWidth;
    fill.style.animation = "";
  }
}

function showSlide(index) {
  const slides = bannerSlides();
  const progressBars = bannerProgressBars();
  if (slides.length === 0) return;

  currentSlide = (index + slides.length) % slides.length;
  slides.forEach((slide, i) =>
    slide.classList.toggle("active", i === currentSlide),
  );
  progressBars.forEach((bar, i) => {
    const active = i === currentSlide;
    bar.classList.toggle("active", active);
    bar.setAttribute("aria-selected", active ? "true" : "false");
    if (active) resetProgressBar(bar);
  });
}

function changeSlide(direction) {
  showSlide(currentSlide + direction);
}

function goToSlide(index) {
  showSlide(index);
}

function startAutoSlide() {
  clearInterval(autoSlideInterval);
  autoSlideInterval = setInterval(() => changeSlide(1), AUTO_SLIDE_INTERVAL_MS);
}

function pauseAutoSlide() {
  clearInterval(autoSlideInterval);
  clearTimeout(autoSlideResumeTimeout);
  autoSlideResumeTimeout = setTimeout(startAutoSlide, 10000);
}

function initBannerSlider() {
  const slides = bannerSlides();
  const progressBars = bannerProgressBars();
  const previousButton = document.querySelector(".prev-btn");
  const nextButton = document.querySelector(".next-btn");
  if (slides.length === 0) return;

  showSlide(0);
  startAutoSlide();

  previousButton?.addEventListener("click", () => {
    changeSlide(-1);
    pauseAutoSlide();
  });
  nextButton?.addEventListener("click", () => {
    changeSlide(1);
    pauseAutoSlide();
  });

  progressBars.forEach((bar, index) => {
    bar.addEventListener("click", () => {
      goToSlide(index);
      pauseAutoSlide();
    });
    bar.addEventListener("keydown", (event) => {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        goToSlide(index);
        pauseAutoSlide();
      }
    });
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "ArrowLeft") {
      changeSlide(-1);
      pauseAutoSlide();
    } else if (event.key === "ArrowRight") {
      changeSlide(1);
      pauseAutoSlide();
    }
  });

  const slider = document.querySelector(".banner-slider");
  let touchStartX = 0;
  slider?.addEventListener(
    "touchstart",
    (event) => {
      touchStartX = event.changedTouches[0].screenX;
    },
    { passive: true },
  );
  slider?.addEventListener(
    "touchend",
    (event) => {
      const distance = event.changedTouches[0].screenX - touchStartX;
      if (Math.abs(distance) >= 50) {
        changeSlide(distance < 0 ? 1 : -1);
        pauseAutoSlide();
      }
    },
    { passive: true },
  );
}

// About tabs - auto-switches back and forth until a tab is clicked.
// Panel switches use the Web Animations API so rapid clicks always play a
// fresh, identical cross-fade in both directions.
let currentAboutTabIndex = 0;
let aboutSlideInterval;
const ABOUT_SLIDE_INTERVAL_MS = 7000;
const PANEL_TRANSITION_MS = 1150;
const REDUCED_MOTION =
  window.matchMedia?.("(prefers-reduced-motion: reduce)").matches || false;

function aboutTabs() {
  return document.querySelectorAll(".about-tab");
}

function aboutPanels() {
  return document.querySelectorAll(".about-tab-panel");
}

function animatePanelOut(panel) {
  panel.getAnimations().forEach((anim) => anim.cancel());
  panel.style.zIndex = "0";
  panel.classList.remove("active");
  if (REDUCED_MOTION || typeof panel.animate !== "function") {
    panel.hidden = true;
    return;
  }
  const animation = panel.animate(
    [
      { opacity: 1, transform: "translateY(0)" },
      { opacity: 0, transform: "translateY(-8px)" },
    ],
    {
      duration: PANEL_TRANSITION_MS,
      easing: "cubic-bezier(0.25, 1, 0.3, 1)",
      fill: "both",
    },
  );
  animation.onfinish = () => {
    if (!panel.classList.contains("active")) panel.hidden = true;
  };
}

function animatePanelIn(panel) {
  panel.getAnimations().forEach((anim) => anim.cancel());
  panel.hidden = false;
  panel.style.zIndex = "1";
  panel.classList.add("active");
  if (REDUCED_MOTION || typeof panel.animate !== "function") return;
  panel.animate(
    [
      { opacity: 0, transform: "translateY(8px)" },
      { opacity: 1, transform: "translateY(0)" },
    ],
    {
      duration: PANEL_TRANSITION_MS,
      easing: "cubic-bezier(0.25, 1, 0.3, 1)",
      fill: "both",
    },
  );
}

function showAboutTab(index) {
  const tabs = aboutTabs();
  const panels = aboutPanels();
  if (tabs.length === 0) return;

  currentAboutTabIndex = (index + tabs.length) % tabs.length;
  tabs.forEach((tab, i) => {
    const active = i === currentAboutTabIndex;
    tab.classList.toggle("active", active);
    tab.setAttribute("aria-selected", active ? "true" : "false");
  });

  panels.forEach((panel) => {
    const show =
      panel.id === `tab-${tabs[currentAboutTabIndex].dataset.tab}`;
    if (show) {
      if (!panel.classList.contains("active")) animatePanelIn(panel);
    } else {
      if (panel.classList.contains("active")) animatePanelOut(panel);
    }
  });
}

function advanceAboutTab() {
  showAboutTab(currentAboutTabIndex + 1);
}

function stopAboutSlide() {
  clearInterval(aboutSlideInterval);
}

function initAboutTabs() {
  const tabs = aboutTabs();
  if (tabs.length === 0) return;

  showAboutTab(0);
  aboutSlideInterval = setInterval(
    advanceAboutTab,
    ABOUT_SLIDE_INTERVAL_MS,
  );

  tabs.forEach((tab, index) => {
    tab.addEventListener("click", () => {
      showAboutTab(index);
      stopAboutSlide();
    });
  });
}

// Navigation
function setActiveNavLink() {
  const sections = document.querySelectorAll("section[id]");
  const links = document.querySelectorAll(".navbar-nav .nav-link");
  let current = "";

  sections.forEach((section) => {
    if (window.scrollY >= section.offsetTop - 200) current = section.id;
  });
  links.forEach((link) => {
    const active = link.getAttribute("href") === `#${current}`;
    link.classList.toggle("active", active);
    if (active) link.setAttribute("aria-current", "page");
    else link.removeAttribute("aria-current");
  });
}

function closeMobileMenu() {
  const collapse = document.querySelector(".navbar-collapse");
  if (collapse?.classList.contains("show") && window.bootstrap?.Collapse) {
    window.bootstrap.Collapse.getOrCreateInstance(collapse).hide();
  }
}

function initNavigation() {
  document.querySelectorAll(".navbar-nav .nav-link").forEach((link) => {
    link.addEventListener("click", (event) => {
      const targetId = link.getAttribute("href");
      if (!targetId?.startsWith("#")) return;

      const target = document.querySelector(targetId);
      if (!target) return;
      event.preventDefault();
      window.scrollTo({
        top: Math.max(0, target.offsetTop - 80),
        behavior: "smooth",
      });
      closeMobileMenu();
    });
  });
}

function initNavbar() {
  const navbar = document.querySelector(".navbar");
  let scrollTicking = false;

  window.addEventListener(
    "scroll",
    () => {
      if (scrollTicking) return;
      window.requestAnimationFrame(() => {
        setActiveNavLink();
        navbar?.classList.toggle("scrolled", window.scrollY > 100);
        scrollTicking = false;
      });
      scrollTicking = true;
    },
    { passive: true },
  );
}

// Contact form
function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showAlert(message, type) {
  const container = document.querySelector(".contact-section .container");
  if (!container) return;
  const alert = document.createElement("div");
  alert.className = `alert alert-${type} alert-dismissible fade show`;
  alert.textContent = message;
  container.prepend(alert);
  setTimeout(() => alert.remove(), 5000);
}

function showSuccessMessage(message) {
  showAlert(message, "success");
}

function showErrorMessage(message) {
  showAlert(message, "danger");
}

function validateForm(data) {
  const errors = [];
  if (!data.name?.trim()) errors.push("Name is required");
  if (!data.email?.trim()) errors.push("Email is required");
  else if (!isValidEmail(data.email))
    errors.push("Please enter a valid email address");
  if (!data.message?.trim()) errors.push("Message is required");
  if (errors.length > 0) showErrorMessage(errors.join(", "));
  return errors.length === 0;
}

function initContactForm() {
  const form = document.querySelector("#contact-form");
  form?.addEventListener("submit", (event) => {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(form));
    if (validateForm(data)) {
      showSuccessMessage(
        "Thank you for your message! We will get back to you soon.",
      );
      form.reset();
    }
  });
}

function initMobileMenu() {
  const toggler = document.querySelector(".navbar-toggler");
  const collapse = document.querySelector(".navbar-collapse");
  if (!toggler || !collapse) return;

  document.addEventListener("click", (event) => {
    if (!collapse.contains(event.target) && !toggler.contains(event.target)) {
      closeMobileMenu();
    }
  });
}

function initImageLoading() {
  const fallbackMap = {
    "assets/images/product_partner/logo_sumato.webp":
      "assets/images/product_partner/logo_sumato.jpg",
    "assets/images/product_partner/logo_bosch.webp":
      "assets/images/product_partner/logo_bosch.jpg",
    "assets/images/product_partner/logo_buccheri_industrial.webp":
      "assets/images/product_partner/logo_buccheri_industrial.jpg",
    "assets/images/product_partner/sata_logo.webp":
      "assets/images/product_partner/sata_logo.jpg",
    "assets/images/product_partner/consumable_logo.webp":
      "assets/images/product_partner/consumable_logo.png",
  };

  document.querySelectorAll("img").forEach((image) => {
    if (!image.hasAttribute("loading")) image.loading = "lazy";
    if (!image.hasAttribute("decoding")) image.decoding = "async";

    const fallback =
      image.dataset.fallback || fallbackMap[image.getAttribute("src")];
    if (!fallback) return;

    image.dataset.fallback = fallback;
    image.addEventListener("error", () => {
      if (image.dataset.fallbackUsed === "true") return;
      image.dataset.fallbackUsed = "true";
      image.src = fallback;
    });
  });
}

function initImageFade() {
  const fadeImages = document.querySelectorAll(".img-fade");
  if (fadeImages.length === 0) return;

  const markLoaded = (image) => {
    image.classList.add("is-loaded");
    const container = image.closest(
      ".gallery-image-container, .client-logo-img",
    );
    container?.classList.add("img-loaded");
  };

  fadeImages.forEach((image) => {
    if (image.complete && image.naturalWidth > 0) {
      markLoaded(image);
      return;
    }
    image.addEventListener("load", () => markLoaded(image), { once: true });
    image.addEventListener("error", () => markLoaded(image), { once: true });
  });
}

function initBgLazyLoad() {
  const sections = document.querySelectorAll("[data-bg-lazy]");
  if (sections.length === 0) return;

  const applyBackground = (section) => {
    const url = section.getAttribute("data-bg-lazy");
    if (url) section.style.backgroundImage = `url("${url}")`;
  };

  if (!("IntersectionObserver" in window)) {
    sections.forEach(applyBackground);
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        applyBackground(entry.target);
        observer.unobserve(entry.target);
      });
    },
    { rootMargin: "300px" },
  );

  sections.forEach((section) => observer.observe(section));
}

function initClientsMarquee() {
  document.querySelectorAll(".clients-track").forEach((track) => {
    if (track.dataset.loopReady === "true") return;

    const originalItems = [...track.children];
    if (originalItems.length === 0) return;

    originalItems.forEach((item) => {
      const clone = item.cloneNode(true);
      clone.setAttribute("aria-hidden", "true");
      clone.querySelectorAll("img").forEach((image) => {
        image.alt = "";
      });
      track.appendChild(clone);
    });

    track.dataset.loopReady = "true";
  });
}

function initGalleryModal() {
  const modalImage = document.querySelector("#galleryModalImage");
  const galleryModal = document.querySelector("#galleryModal");
  if (!modalImage || !galleryModal) return;

  const openFullscreen = (container) => {
    const trigger = container.querySelector("[data-gallery-image]");
    const sourceImage = container.querySelector(".gallery-image");
    if (!trigger || !sourceImage) return;
    const source = trigger.getAttribute("data-gallery-image");
    if (!source) return;

    modalImage.src = source;
    modalImage.alt = sourceImage.alt || "Gallery image";
    modalImage.onerror = () => {
      modalImage.removeAttribute("src");
      modalImage.alt = "Gallery image unavailable";
    };

    if (window.bootstrap?.Modal) {
      window.bootstrap.Modal.getOrCreateInstance(galleryModal).show();
    }
  };

  document.querySelectorAll(".gallery-image-container").forEach((container) => {
    container.querySelectorAll("[data-gallery-image]").forEach((trigger) => {
      trigger.addEventListener("click", () => openFullscreen(container));
    });
    container
      .querySelector(".gallery-image")
      ?.addEventListener("click", () => openFullscreen(container));
    container.querySelectorAll(".gallery-image").forEach((image) => {
      image.tabIndex = 0;
      image.setAttribute("role", "button");
      image.setAttribute(
        "aria-label",
        image.alt + " (open fullscreen)",
      );
      image.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          openFullscreen(container);
        }
      });
    });
  });
}

function initMotionEffects() {
  if (!window.gsap || !window.ScrollTrigger) return;

  const { gsap } = window;
  gsap.registerPlugin(window.ScrollTrigger);

  const motionTargets = [
    ".section-title",
    ".about-layout",
    ".partners-grid .partner-card",
    ".clients-track .client-logo",
    ".gallery-item",
    ".value-card",
    ".cta-strip",
    ".contact-list",
    ".office-block",
    ".contact-social-icons",
  ];

  motionTargets.forEach((selector) => {
    document.querySelectorAll(selector).forEach((element) => {
      element.classList.add("motion-reveal");
    });
  });

  const counterAnimation = () => {
    document
      .querySelectorAll(".about-stat strong[data-target]")
      .forEach((el) => {
        if (el.dataset.animated === "true") return;
        el.dataset.animated = "true";
        const targetValue = Number(el.dataset.target || 0);
        const currentValue = { value: 0 };

        gsap.to(currentValue, {
          value: targetValue,
          duration: 1.2,
          ease: "power2.out",
          onUpdate: () => {
            el.textContent = `${Math.round(currentValue.value)}`;
          },
        });
      });
  };

  gsap.fromTo(
    ".hero-section .banner-slider",
    { scale: 1.06, opacity: 0.8 },
    {
      scale: 1,
      opacity: 1,
      duration: 1.25,
      ease: "power2.out",
      delay: 0.12,
    },
  );

  gsap.fromTo(
    ".banner-nav-btn, .banner-progress-bar",
    { opacity: 0, y: 14 },
    {
      opacity: 1,
      y: 0,
      duration: 0.7,
      stagger: 0.08,
      ease: "power2.out",
      delay: 0.3,
    },
  );

  gsap.utils.toArray(".section-title").forEach((title) => {
    gsap.fromTo(
      title,
      { opacity: 0, y: 24 },
      {
        opacity: 1,
        y: 0,
        duration: 0.8,
        ease: "power3.out",
        scrollTrigger: {
          trigger: title,
          start: "top 85%",
        },
      },
    );
  });

  gsap.fromTo(
    ".about-stat",
    { opacity: 0, y: 18 },
    {
      opacity: 1,
      y: 0,
      duration: 0.7,
      stagger: 0.12,
      ease: "power3.out",
      scrollTrigger: {
        trigger: ".about-section",
        start: "top 72%",
        onEnter: counterAnimation,
      },
    },
  );

  gsap.fromTo(
    ".about-layout, .partners-grid .partner-card",
    { opacity: 0, y: 34, scale: 0.98 },
    {
      opacity: 1,
      y: 0,
      scale: 1,
      duration: 0.9,
      stagger: 0.1,
      ease: "power3.out",
      scrollTrigger: {
        trigger: ".about-section, .partners-section",
        start: "top 78%",
      },
    },
  );

  // Hover lift driven by GSAP so it layers on top of the entrance tween
  // (a CSS transform on the card would be overridden by GSAP's inline
  // transform). Only 'y' is touched, so the fade-in's opacity/scale are
  // never interfered with.
  gsap.utils.toArray(".partners-grid .partner-card").forEach((card) => {
    card.addEventListener("pointerenter", () => {
      gsap.to(card, { y: -10, duration: 0.35, ease: "power2.out" });
    });
    card.addEventListener("pointerleave", () => {
      gsap.to(card, { y: 0, duration: 0.45, ease: "power2.out" });
    });
  });

  gsap.fromTo(
    ".gallery-item",
    { opacity: 0, y: 30, scale: 0.97 },
    {
      opacity: 1,
      y: 0,
      scale: 1,
      duration: 0.8,
      ease: "power3.out",
      stagger: 0,
      scrollTrigger: {
        trigger: ".gallery-section",
        start: "top 78%",
      },
    },
  );

  gsap.fromTo(
    ".value-card, .cta-strip",
    { opacity: 0, y: 28 },
    {
      opacity: 1,
      y: 0,
      duration: 0.6,
      stagger: 0,
      ease: "linear",
      delay: 0,
      scrollTrigger: {
        trigger: ".why-us-section, .cta-strip-section",
        start: "top 82%",
      },
    },
  );

  gsap.utils.toArray(".clients-track .client-logo").forEach((logo, index) => {
    gsap.fromTo(
      logo,
      { opacity: 0, scale: 0.8 },
      {
        opacity: 1,
        scale: 1,
        duration: 0.7,
        delay: index * 0.03,
        ease: "power2.out",
        scrollTrigger: {
          trigger: logo,
          start: "top 95%",
        },
      },
    );
  });

  gsap.fromTo(
    ".contact-list, .office-block, .contact-social-icons",
    { opacity: 0, y: 24 },
    {
      opacity: 1,
      y: 0,
      duration: 0.7,
      stagger: 0.12,
      ease: "power2.out",
      scrollTrigger: {
        trigger: ".contact-section",
        start: "top 78%",
      },
    },
  );

  gsap.to(".clients-track", {
    xPercent: -50,
    ease: "none",
    duration: 30,
    repeat: -1,
    scrollTrigger: {
      trigger: ".clients-section",
      start: "top center",
      end: "bottom top",
      scrub: 1,
    },
  });

  document.querySelectorAll(".cta-strip-btn").forEach((button) => {
    button.addEventListener("pointermove", (event) => {
      const bounds = button.getBoundingClientRect();
      const x = event.clientX - bounds.left;
      const y = event.clientY - bounds.top;
      const offsetX = (x / bounds.width - 0.5) * 14;
      const offsetY = (y / bounds.height - 0.5) * 14;
      gsap.to(button, {
        x: offsetX,
        y: offsetY,
        duration: 0.25,
        ease: "power2.out",
      });
    });

    button.addEventListener("pointerleave", () => {
      gsap.to(button, {
        x: 0,
        y: 0,
        duration: 0.35,
        ease: "power2.out",
      });
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  document.documentElement.classList.add("js-img-fade");
  initImageLoading();
  initClientsMarquee();
  initImageFade();
  initBgLazyLoad();
  initBannerSlider();
  initAboutTabs();
  initNavigation();
  initNavbar();
  initContactForm();
  initMobileMenu();
  initGalleryModal();
  setActiveNavLink();
  initMotionEffects();
});
