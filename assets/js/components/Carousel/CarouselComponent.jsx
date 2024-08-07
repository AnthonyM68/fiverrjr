import React, { useEffect, useState } from "react";
import { createRoot } from 'react-dom/client';
import { Card, Image } from "semantic-ui-react";
import Carousel from "react-multi-carousel";
import { useInView } from "react-intersection-observer";

const BestServicesCarousel = () => {
  const [lastService, setLastService] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (window.__INITIAL_DATA__ && window.__INITIAL_DATA__.lastService) {
      setLastService(window.__INITIAL_DATA__.lastService);
      setLoading(false);
    } else {
      console.error("window.__INITIAL_DATA__ is not defined or does not contain lastService");
    }
  }, []);

  const responsive = {
    desktop: { breakpoint: { max: 3000, min: 1024 }, items: 4 },
    tablet: { breakpoint: { max: 1024, min: 768 }, items: 3 },
    mobile: { breakpoint: { max: 768, min: 320 }, items: 2 },
    mobile: { breakpoint: { max: 320, min: 0 }, items: 1 },
  };

  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.5,
  });

  if (loading) {
    return <div className="ui active centered inline loader"></div>;
  }

  return (
    <div className="best-services-carousel">
      <h2
        ref={ref}
        className={`module-title ${inView ? "uk-animation-slide-right" : ""}`}
      >
        Derniers services
      </h2>

      <Carousel responsive={responsive} infinite autoPlay={false}>
        {lastService.length > 0 ? (
          lastService.map((service, index) => {
            const imageUrl = service.picture;

            return (
              <div key={index} className="carousel-container-item">
                <Card 
                className={`carousel-item  ${inView ? "uk-animation-fade" : ""}`}>
                  {imageUrl && <Image src={imageUrl} wrapped ui={false} />}
                  <Card.Content>
                    <Card.Header>{service.title}</Card.Header>
                    <Card.Meta>
                      <span className="date">
                        Par {service.user.firstName} {service.user.lastName}
                      </span>
                    </Card.Meta>
                    <Card.Description>{service.description}</Card.Description>
                  </Card.Content>
                </Card>
              </div>
            );
          })
        ) : (
          <p>Aucun service disponible.</p>
        )}
      </Carousel>
    </div>
  );
};

document.addEventListener("DOMContentLoaded", () => {
  console.log("==> CarouselComponent.jsx");

  const carouselRoot = document.getElementById('bestservices-root');
  if (carouselRoot) {
    const root = createRoot(carouselRoot);
    root.render(<BestServicesCarousel />);
  }
});

export { BestServicesCarousel };
