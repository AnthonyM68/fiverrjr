import React, { useEffect, useState } from "react";
import { useInView } from "react-intersection-observer";
import { createRoot } from "react-dom/client";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import "slick-carousel";

const ParallaxHome = ({ id }) => {
  useEffect(() => {
    // Initialisation du carousel
    // Initialisation du carousel
    $(`.ad-carousel-${id}`).slick({
      dots: true,
      infinite: true,
      speed: 500,
      slidesToShow: 5, // Modifiez ici pour afficher le nombre souhaité
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 3000, // Ajustez pour ralentir ou accélérer
      arrows: false,
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
          },
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
      ],
    });
  }, [id]);

  // State pour suivre si l'élément est en vue
  const [ref, inView] = useInView({
    triggerOnce: true, // Déclenche l'observation une seule fois
    threshold: 0.5, // Détecte lorsque 50% de l'élément est visible
  });
  return (
    <div className="parallax-container">
      <div className="parallax-content">
        <div className="overlay-parallax-home"></div>
        <div className="parallax parallax-home"></div>
        <div
          ref={ref}
          className={`${
            inView ? "uk-animation-fade floating-title" : "floating-title"
          }`}
        >
          <div className="title-container">
            <div
              ref={ref}
              className={`${
                inView
                  ? "fiverr-animate uk-animation-slide-left"
                  : "fiverr-animate"
              }`}
            >
              Fiverr
            </div>
            <div
              ref={ref}
              className={`${
                inView
                  ? "junior-animate uk-animation-slide-right"
                  : "junior-animate"
              }`}
            >
              Junior
            </div>
          </div>
          <p className="sub-animate uk-animation-slide-bottom-medium">
            Rejoignez une communauté de talents
          </p>
        </div>
      </div>
      <div className="ad-banner ">
        <div className="custom-container text-center">
          <div className={`ad-carousel-${id} ad-carousel`}>
            <div>
              <a href="https://example.com/codealsace" target="_blank">
                CodeAlsace
              </a>
            </div>
            <div>
              <a href="https://example.com/techhautrhin" target="_blank">
                TechHautRhin
              </a>
            </div>
            <div>
              <a href="https://example.com/innostart-alsace" target="_blank">
                InnoStart Alsace
              </a>
            </div>
            <div>
              <a href="https://example.com/devfactory-hautrhin" target="_blank">
                DevFactory Haut-Rhin
              </a>
            </div>
            <div>
              <a
                href="https://example.com/alsace-digital-academy"
                target="_blank"
              >
                Alsace Digital Academy
              </a>
            </div>
            <div>
              <a href="https://example.com/starthub68" target="_blank">
                StartHub 68
              </a>
            </div>
            <div>
              <a href="https://example.com/innovit-hautrhin" target="_blank">
                InnovIT Haut-Rhin
              </a>
            </div>
          </div>
          <h2 className="ui center aligned icon header">
            <img
              src="/uploads/services/alsace.png"
              alt="Alsace region Icon"
              className="circular icon"
            />
          </h2>
        </div>
        <div className="separator"></div>
      </div>
    </div>
  );
};

const ParallaxHomeFooter = () => {
  // State pour suivre si l'élément est en vue
  const [ref, inView] = useInView({
    triggerOnce: true, // Déclenche l'observation une seule fois
    threshold: 0.5, // Détecte lorsque 50% de l'élément est visible
  });
  return (
    <div className="parallax-container">
      <div className="parallax-content">
        <div className="overlay-parallax-footer"></div>
        <div className="parallax parallax-footer"></div>
        <div className="floating-text uk-animation-fade">
          <h1>
            <span
              ref={ref}
              className={`${
                inView
                  ? "text-primary uk-animation-slide-left-medium"
                  : "text-primary"
              }`}
            >
              <span>Fiverr</span> <span>Junior</span>
            </span>
            <span
              ref={ref}
              className={`${
                inView ? "text-sub uk-animation-slide-right-medium" : "text-sub"
              }`}
            >
              Vos projets, nos meilleurs talents.
            </span>
          </h1>
        </div>
      </div>
    </div>
  );
};
document.addEventListener("DOMContentLoaded", () => {
  console.log("==> Parallax.jsx");

  const parallaxHome = document.getElementById("ParallaxHome");

  if (parallaxHome) {
    const root = createRoot(parallaxHome);
    root.render(<ParallaxHome id="1" />);
  }
  const parallaxFooter = document.getElementById("ParallaxFooter");

  if (parallaxFooter) {
    const root = createRoot(parallaxFooter);
    root.render(<ParallaxHomeFooter />);
  }
});
export { ParallaxHome, ParallaxHomeFooter };
