
import React from 'react';
import { Card, Icon, Image } from 'semantic-ui-react';
// import { Carousel } from 'react-responsive-carousel'; // simple
import Carousel from 'react-multi-carousel'; // multi
import { useInView } from 'react-intersection-observer';



const CarouselComponent = () => {
  return (
    <div className="carousel-wrapper">
      <Carousel
        showArrows={false}
        infiniteLoop={true}
        showThumbs={false}
        autoPlay={true}
        interval={3000}
        showStatus={false}
      >
        <div>
          <img src="https://via.placeholder.com/600x400" alt="Image 1" />

        </div>
        <div>
          <img src="https://via.placeholder.com/600x400" alt="Image 2" />

        </div>
        <div>
          <img src="https://via.placeholder.com/600x400" alt="Image 3" />
        </div>
      </Carousel>
    </div>
  );
};


const BestServicesCarousel = ({ services = [] }) => {
  if (services.length === 0) {
    return <div>Aucun service disponible</div>;
  }
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
  // State pour suivre si l'élément est en vue
  const [ref, inView] = useInView({
    triggerOnce: true, // Déclenche l'observation une seule fois
    threshold: 0.5, // Détecte lorsque 50% de l'élément est visible
  });
  return (
    <div className="best-services-carousel">
      <h2 ref={ref} className={`module-title ${inView ? 'uk-animation-slide-right' : ''}`}>Meilleurs services</h2>
      <Carousel responsive={responsive} infinite autoPlay={false}>
        {services.map((service, index) => (
          <div key={index}>
            <Card>
              <Image src={service.image} wrapped ui={false} />
              <Card.Content>
                <Card.Header>{service.title}</Card.Header>
                <Card.Meta>
                  <span className="date">Par {service.username}</span>
                </Card.Meta>
                <Card.Description>{service.description}</Card.Description>
              </Card.Content>
              <Card.Content extra>
                <a>
                  <Icon name="user" />
                  {service.reviews} Avis
                </a>
              </Card.Content>
            </Card>
          </div>
        ))}
      </Carousel>
    </div>
  );
};
// Utiliser les paramètres par défaut en JavaScript
BestServicesCarousel.defaultProps = {
  services: []
};

export { CarouselComponent, BestServicesCarousel };