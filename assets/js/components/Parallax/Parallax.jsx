import React, { useEffect } from "react";
import { createRoot } from "react-dom/client";
import { useInView } from 'react-intersection-observer';
import "semantic-ui-css/semantic.min.css";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import $ from "jquery";
import "slick-carousel";

const Parallax = ({ id, height_container, height_parallax }) => {
  useEffect(() => {
    // Initialisation du carousel avec une classe spécifique
    $(`.ad-carousel-${id}`).slick({
      dots: true,
      infinite: true,
      speed: 500,
      slidesToShow: 6,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 2000,
      arrows: false,
    });
  }, [id]);
  // State pour suivre si l'élément est en vue
  const [ref, inView] = useInView({
    triggerOnce: true, // Déclenche l'observation une seule fois
    threshold: 0.5, // Détecte lorsque 50% de l'élément est visible
  });
  return (
    <div className="parallax-container" style={{ height: height_container }}>
      <div className="parallax-content" style={{ height: height_parallax }}>
        <div
          className={`parallax parallax-home-${id}`}
          style={{ height: height_parallax }}
        ></div>
        <div className="floating-text">
          <h1>
            {/* <div className="fiverr-animate uk-animation-slide-left">Fiverr</div> */}
            <div ref={ref} className={`module-title ${inView ? 'fiverr-animate uk-animation-slide-left' : ''}`}>Fiverr</div>
            {/* <div className="junior-animate uk-animation-slide-right">Junior</div> */}
            <div ref={ref} className={`module-title ${inView ? 'junior-animate uk-animation-slide-right' : ''}`}>Junior</div>
          </h1>
          <p className="sub-animate uk-animation-slide-bottom-medium">Rejoignez une communauté de talents</p>
        </div>
        {/* <div className="overlay"></div> */}
        <div className="ad-banner uk-animation-fade">
          <div className="custom-container text-center">
            <div className={`ad-carousel-${id} text-carousel-parallax`}>
              <div className="">
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
                <a
                  href="https://example.com/devfactory-hautrhin"
                  target="_blank"
                >
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
                src="/img/services/alsace.png"
                alt="Friends Icon"
                className="circular icon"
              />
            </h2>
          </div>
        </div>
      </div>
    </div>
  );
};

const ParallaxWithoutContent = ({ id, height_container, height_parallax }) => {
  return (
    <div className="parallax-container" style={{ height: height_container }}>
      <div className="parallax-content" style={{ height: height_parallax }}>
        <div
          className={`parallax parallax-home-${id}`}
          style={{ height: height_parallax }}
        ></div>
        <div className="floating-text">
          <h1>
            <span class="text-primary">Fiverr Junior</span>
            <span class="text-sub">Vos projets, nos meilleurs talents.</span>
          </h1>
        </div>
      </div>
    </div>
  );
};

export { Parallax, ParallaxWithoutContent };

const parallaxRoot = document.getElementById("parallax-root");
if (parallaxRoot) {
  const root = createRoot(parallaxRoot);

  root.render(
    <Parallax id="1" height_container="850px" height_parallax="650px" />
  );
}

const parallaxRoot2 = document.getElementById("parallax-root-2");
if (parallaxRoot2) {
  const root = createRoot(parallaxRoot2);

  root.render(
    <ParallaxWithoutContent
      id="2"
      height_container="400px"
      height_parallax="400px"
    />
  );
}
