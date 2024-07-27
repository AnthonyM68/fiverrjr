
import React from 'react';
import { Card, Icon, Image } from 'semantic-ui-react';
// import { Carousel } from 'react-responsive-carousel'; // simple
import Carousel from 'react-multi-carousel'; // multi
import { useInView } from 'react-intersection-observer';
import config from '../../config'


const BestServicesCarousel = ({ services = [] }) => {

  // console.log('Received services:', services);
  // const assetBaseUrl = process.env.REACT_APP_ASSET_BASE_URL || '';
  
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
      <h2 ref={ref} className={`module-title ${inView ? 'uk-animation-slide-right' : ''}`}>Deniers services</h2>


      <Carousel responsive={responsive} infinite autoPlay={false}>
        {services.map((service, index) => {
          // Log each service to inspect its structure
          // console.log(`Service ${index}:`, service);

          // Check if the service object has the expected properties
          if (!service.title || !service.picture || !service.description) {
            // console.error(`Service ${index} is missing required properties.`);
            return <div key={index}>Invalid service data</div>;
          }

          return (
            <div key={index}>
              <Card>
                <Image src={`${config.ASSET_BASE_URL}/img/services/${service.picture}`} wrapped ui={false} />
                <Card.Content>
                  <Card.Header>{service.title}</Card.Header>
                  <Card.Meta>
                    <span className="date">Par {service.user.firstName} {service.user.lastName}</span>
                  </Card.Meta>
                  <Card.Description>{service.description}</Card.Description>
                </Card.Content>
                <Card.Content extra>
                  {/* <a>
                    <Icon name="user" />
                     Avis
                  </a> */}
                </Card.Content>
              </Card>
            </div>
          );
        })}
      </Carousel>



    </div>
  );
};
// Utiliser les paramètres par défaut en JavaScript
// BestServicesCarousel.defaultProps = {
//   services: []
// };

export { BestServicesCarousel };