"use strict";

// Banner slider
let currentSlide = 0;
let autoSlideInterval;
let autoSlideResumeTimeout;
const AUTO_SLIDE_INTERVAL_MS = 8000;

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
    slides.forEach((slide, i) => {
        const active = i === currentSlide;
        slide.classList.toggle("active", active);

        if (active) {
            const animatedElements = slide.querySelectorAll(
                ".banner-image, .hero-eyebrow, .hero-copy h1, .hero-copy h2, .hero-copy p, .hero-cta",
            );
            animatedElements.forEach((element) => {
                element.style.animation = "none";
            });
            void slide.offsetWidth;
            animatedElements.forEach((element) => {
                element.style.removeProperty("animation");
            });
        }
    });
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
    autoSlideInterval = setInterval(
        () => changeSlide(1),
        AUTO_SLIDE_INTERVAL_MS,
    );
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
    aboutSlideInterval = setInterval(advanceAboutTab, ABOUT_SLIDE_INTERVAL_MS);

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
    if (sections.length === 0) return;
    const links = document.querySelectorAll(".navbar-nav .nav-link");
    const navbar = document.querySelector(".navbar");
    const marker = window.scrollY + (navbar?.offsetHeight ?? 80) + 24;
    let current = sections[0]?.id ?? "";

    sections.forEach((section) => {
        if (section.getBoundingClientRect().top + window.scrollY <= marker) {
            current = section.id;
        }
    });
    links.forEach((link) => {
        const href = link.getAttribute("href");
        let targetId = link.dataset.navSection || "";
        if (!targetId) {
            try {
                targetId = new URL(href, window.location.href).hash.slice(1);
            } catch {
                targetId = "";
            }
        }
        const active = targetId !== "" && targetId === current;
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
    document
        .querySelectorAll(".navbar-nav .nav-link, .hero-cta")
        .forEach((link) => {
            link.addEventListener("click", (event) => {
                const targetUrl = new URL(
                    link.getAttribute("href"),
                    window.location.href,
                );
                if (
                    targetUrl.origin !== window.location.origin ||
                    targetUrl.pathname !== window.location.pathname ||
                    !targetUrl.hash
                )
                    return;

                const target = document.querySelector(targetUrl.hash);
                if (!target) return;
                event.preventDefault();
                window.scrollTo({
                    top: Math.max(
                        0,
                        target.getBoundingClientRect().top +
                            window.scrollY -
                            (document.querySelector(".navbar")?.offsetHeight ??
                                80),
                    ),
                    behavior: "smooth",
                });
                window.history.replaceState(null, "", targetUrl.hash);
                closeMobileMenu();
            });
        });
}

function initNavbar() {
    const navbar = document.querySelector(".navbar");
    const hero = document.querySelector(".hero-section");
    let scrollTicking = false;

    const updateNavbar = () => {
        const heroBottom = hero ? hero.getBoundingClientRect().bottom : 0;
        navbar?.classList.toggle(
            "scrolled",
            !hero || heroBottom <= (navbar?.offsetHeight ?? 80),
        );
        setActiveNavLink();
    };

    updateNavbar();

    window.addEventListener(
        "scroll",
        () => {
            if (scrollTicking) return;
            window.requestAnimationFrame(() => {
                updateNavbar();
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
        if (
            !collapse.contains(event.target) &&
            !toggler.contains(event.target)
        ) {
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
        image.addEventListener("error", () => markLoaded(image), {
            once: true,
        });
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

        const initialItems = [...track.children];
        if (initialItems.length === 0) return;

        const showcase = track.closest(".clients-showcase");
        const staticList = showcase?.querySelector("[data-client-static-list]");
        const showcaseContent = showcase?.querySelector(
            ".clients-showcase-content",
        );
        const showAllButton = showcase?.querySelector("[data-client-show-all]");
        const showCarouselButton = showcase?.querySelector(
            "[data-client-show-carousel]",
        );
        const marqueeWidth = track.parentElement?.clientWidth ?? 0;
        const cloneLogo = (item, decorative = true) => {
            const clone = item.cloneNode(true);
            if (decorative) {
                clone.setAttribute("aria-hidden", "true");
                clone.querySelectorAll("img").forEach((image) => {
                    image.alt = "";
                });
            }
            return clone;
        };

        initialItems.forEach((item) => {
            staticList?.appendChild(cloneLogo(item, false));
        });

        let repeatedItems = 1;

        while (track.scrollWidth < marqueeWidth && repeatedItems < 20) {
            initialItems.forEach((item) => {
                track.appendChild(cloneLogo(item));
            });
            repeatedItems += 1;
        }

        [...track.children].forEach((item) => {
            track.appendChild(cloneLogo(item));
        });

        track.dataset.loopReady = "true";
        let staticListCollapseTimer;
        let marqueeHideTimer;

        showAllButton?.addEventListener("click", () => {
            if (!staticList || !showCarouselButton) return;
            window.clearTimeout(staticListCollapseTimer);
            window.clearTimeout(marqueeHideTimer);
            showcase.classList.add("is-static");
            showcaseContent?.classList.add("is-showing-all");
            staticList.hidden = false;
            staticList.style.maxHeight = "0px";
            staticList.style.opacity = "0";
            track.style.animationPlayState = "paused";
            showAllButton.hidden = true;
            showAllButton.setAttribute("aria-expanded", "true");
            showCarouselButton.hidden = false;
            requestAnimationFrame(() => {
                staticList.style.maxHeight = `${staticList.scrollHeight + 36}px`;
                staticList.style.opacity = "1";
            });
            marqueeHideTimer = window.setTimeout(() => {
                if (showcase.classList.contains("is-static")) {
                    track.parentElement.hidden = true;
                }
            }, 950);
            staticListCollapseTimer = window.setTimeout(() => {
                if (showcase.classList.contains("is-static")) {
                    staticList.classList.add("is-expanded");
                    staticList.style.maxHeight = "none";
                    staticList.style.overflow = "visible";
                }
            }, 950);
            showCarouselButton.focus();
        });

        showCarouselButton?.addEventListener("click", () => {
            if (!staticList || !showAllButton) return;
            window.clearTimeout(staticListCollapseTimer);
            window.clearTimeout(marqueeHideTimer);
            showcase.classList.remove("is-static");
            track.parentElement.hidden = false;
            staticList.classList.remove("is-expanded");
            staticList.style.overflow = "hidden";
            staticList.style.maxHeight = `${staticList.scrollHeight}px`;
            staticList.style.opacity = "1";
            requestAnimationFrame(() => {
                showcaseContent?.classList.remove("is-showing-all");
                staticList.style.maxHeight = "0px";
                staticList.style.opacity = "0";
            });
            track.style.removeProperty("animation-play-state");
            showCarouselButton.hidden = true;
            showAllButton.hidden = false;
            showAllButton.setAttribute("aria-expanded", "false");
            staticListCollapseTimer = window.setTimeout(() => {
                staticList.hidden = true;
                staticList.removeAttribute("style");
            }, 950);
            showAllButton.focus();
        });
    });
}

function initPartnerProductCarousel() {
    const showcase = document.querySelector("[data-partner-product-showcase]");
    if (!showcase) return;

    const products = [...showcase.querySelectorAll(".partner-product-card")];
    const track = showcase.querySelector("[data-partner-product-track]");
    const controls = showcase.querySelector("[data-partner-product-controls]");
    const previousButton = showcase.querySelector(
        "[data-partner-product-previous]",
    );
    const nextButton = showcase.querySelector("[data-partner-product-next]");

    const visibleProducts = () => products.filter((product) => !product.hidden);

    const updateCarouselControls = () => {
        if (!track || !controls || !previousButton || !nextButton) return;

        const canScroll = track.scrollWidth > track.clientWidth + 2;
        controls.hidden = !canScroll;
        previousButton.disabled = !canScroll || track.scrollLeft <= 2;
        nextButton.disabled =
            !canScroll ||
            track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
    };

    const scrollCarousel = (direction) => {
        if (!track) return;

        const firstProduct = visibleProducts()[0];
        if (!firstProduct) return;

        const gap =
            Number.parseFloat(window.getComputedStyle(track).columnGap) || 16;
        track.scrollBy({
            left:
                direction * (firstProduct.getBoundingClientRect().width + gap),
            behavior: "smooth",
        });
    };

    previousButton?.addEventListener("click", () => scrollCarousel(-1));
    nextButton?.addEventListener("click", () => scrollCarousel(1));
    track?.addEventListener("scroll", updateCarouselControls, {
        passive: true,
    });
    window.addEventListener("resize", updateCarouselControls);
    window.requestAnimationFrame(updateCarouselControls);
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

    document
        .querySelectorAll(".gallery-image-container")
        .forEach((container) => {
            container
                .querySelectorAll("[data-gallery-image]")
                .forEach((trigger) => {
                    trigger.addEventListener("click", () =>
                        openFullscreen(container),
                    );
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

    const prefersReducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;

    if (prefersReducedMotion) return;

    const revealOffset = window.matchMedia("(max-width: 767px)").matches
        ? 14
        : 20;

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

    const sectionConfigs = [
        // 1. About Us
        {
            section: "#about",
            part1: [
                ".about-overview",
                ".about-visual",
                ".about-purpose",
            ],
            part2: [
                ".clients-showcase-info",
                ".clients-showcase-content",
            ],
            onEnter: counterAnimation,
        },
        // 2. Partners
        {
            section: "#partners",
            part1: [
                ".partners-intro",
                ".partners-brand-panel",
            ],
            part2: [
                ".partner-product-showcase",
                ".partners-trust-point",
            ],
        },
        // 3. Why Choose Us
        {
            section: "#why-us",
            part1: [
                ".why-us-intro",
                ".value-card",
            ],
            part2: [
                ".cta-strip",
            ],
        },
        // 4. Gallery (Single key point)
        {
            section: "#gallery",
            elements: [
                "#gallery .section-title",
                ".gallery-item",
            ],
        },
        // 5. Recent Articles (Single key point)
        {
            section: "#recent-articles",
            elements: [
                ".recent-articles-heading",
                ".recent-article-card",
            ],
        },
        // 6. Find Us / Contact (Single key point)
        {
            section: "#contact",
            elements: [
                "#contact .section-title",
                ".office-block",
                ".contact-item",
                ".contact-social",
            ],
        },
    ];

    const resolveElements = (container, selectorsOrFn) => {
        if (typeof selectorsOrFn === "function") {
            return selectorsOrFn(container).filter((el) => el && !el.hidden);
        }
        const elements = [];
        selectorsOrFn.forEach((selector) => {
            const matches = container.matches(selector)
                ? [container]
                : Array.from(container.querySelectorAll(selector));
            matches.forEach((el) => {
                if (!el.hidden && !elements.includes(el)) {
                    elements.push(el);
                }
            });
        });
        return elements;
    };

    sectionConfigs.forEach((config) => {
        const sectionElement = document.querySelector(config.section);
        if (!sectionElement) return;

        const isTwoPart = Boolean(config.part1 && config.part2);
        const part1Elements = isTwoPart
            ? resolveElements(sectionElement, config.part1)
            : resolveElements(
                  sectionElement,
                  config.elements || config.part1 || [],
              );
        const part2Elements = isTwoPart
            ? resolveElements(sectionElement, config.part2)
            : [];
        const allElements = [...part1Elements, ...part2Elements];

        if (allElements.length === 0) return;

        allElements.forEach((element) => {
            element.classList.add("motion-reveal", "motion-revealing");
        });

        gsap.set(allElements, { autoAlpha: 0, x: 0, y: revealOffset });

        window.ScrollTrigger.create({
            trigger: sectionElement,
            start: "top 60%",
            once: true,
            onEnter: () => {
                if (config.onEnter) {
                    config.onEnter();
                }

                // Part 1 / Single-phase reveal: Starts immediately
                if (part1Elements.length > 0) {
                    gsap.to(part1Elements, {
                        autoAlpha: 1,
                        x: 0,
                        y: 0,
                        duration: 0.65,
                        ease: "power2.out",
                        delay: 0,
                        stagger: 0.06,
                        force3D: true,
                        overwrite: true,
                        onComplete: () => {
                            part1Elements.forEach((el) => {
                                el.classList.remove(
                                    "motion-reveal",
                                    "motion-revealing",
                                );
                            });
                            gsap.set(part1Elements, {
                                clearProps: "transform,opacity,visibility",
                            });
                        },
                    });
                }

                // Part 2: Starts 0.5s after Part 1
                if (part2Elements.length > 0) {
                    gsap.to(part2Elements, {
                        autoAlpha: 1,
                        x: 0,
                        y: 0,
                        duration: 0.65,
                        ease: "power2.out",
                        delay: 0.5,
                        stagger: 0.06,
                        force3D: true,
                        overwrite: true,
                        onComplete: () => {
                            part2Elements.forEach((el) => {
                                el.classList.remove(
                                    "motion-reveal",
                                    "motion-revealing",
                                );
                            });
                            gsap.set(part2Elements, {
                                clearProps: "transform,opacity,visibility",
                            });
                        },
                    });
                }
            },
        });
    });

    window.addEventListener("load", () => {
        window.ScrollTrigger?.refresh();
    });

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
    initPartnerProductCarousel();
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
