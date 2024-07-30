import React, { useEffect, useState } from "react";
import { Card, Image } from "semantic-ui-react";
import Carousel from "react-multi-carousel";
import { useInView } from "react-intersection-observer";
// import axios from 'axios';

const BestServicesCarousel = () => {
  // État initial basé sur les données injectées par Twig
  const [lastService, setLastService] = useState(null);
  const [loading, setLoading] = useState(true);
  // Utilisez useEffect pour initialiser les états
  useEffect(() => {
    if (window.__INITIAL_DATA__) {
      console.log("Last Services Data:", window.__INITIAL_DATA__.lastService);
      setLastService(window.__INITIAL_DATA__.lastService || null);
      setLoading(false);
    } else {
      console.error("window.__INITIAL_DATA__ is not defined");
      setLoading(true);
    }
  }, []);
  const responsive = {
    desktop: {
      breakpoint: { max: 3000, min: 1024 },
      items: 3,
    },
    tablet: {
      breakpoint: { max: 1024, min: 464 },
      items: 2,
    },
    mobile: {
      breakpoint: { max: 464, min: 0 },
      items: 1,
    },
  };

  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.5,
  });

  if (loading) {
    return <div className="ui active inline loader"></div>;
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
        {lastService.map((service, index) => {
          const imageUrl = service.picture;

          return (
            <div key={index}>
              <Card>
                {imageUrl ? <Image src={imageUrl} wrapped ui={false} /> : null}
                <Card.Content>
                  <Card.Header>{service.title}</Card.Header>
                  <Card.Meta>
                    <span className="date">
                      Par {service.user.firstName} {service.user.lastName}
                    </span>
                  </Card.Meta>
                  <Card.Description>{service.description}</Card.Description>
                </Card.Content>
                <Card.Content extra></Card.Content>
              </Card>
            </div>
          );
        })}
      </Carousel>
    </div>
  );
};

export { BestServicesCarousel };
