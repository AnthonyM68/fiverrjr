import React, { useEffect, useState, Suspense, lazy } from "react";
import { createRoot } from 'react-dom/client';
import { Card, Image } from "semantic-ui-react";
import { useInView } from "react-intersection-observer";
import './../../../styles/carouselComponent.scss';

const Carousel = lazy(() => import('react-multi-carousel'));

const BestServicesCarousel = () => {
  
  const [lastService, setLastService] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (window.__INITIAL_DATA__ && window.__INITIAL_DATA__.lastService) {
      console.log("CarouselComponent Service Data:", window.__INITIAL_DATA__.lastService);
      const serviceImages = window.__INITIAL_DATA__.lastService.map(service => service.picture).filter(Boolean);
      
      // preloadImages(serviceImages).then(() => {
        setLastService(window.__INITIAL_DATA__.lastService);
        setLoading(false);
      // });
    } else {
      console.error("window.__INITIAL_DATA__ is not defined or does not contain lastService");
      setLoading(false);
    }
  }, []);

// Fonction pour vérifier le chargement des images
const checkImg = (url) => {
  return new Promise((resolve) => {
    const img = new Image();
    img.onload = () => resolve(true);
    img.onerror = () => resolve(false);
    img.src = url;
  });
};

// Fonction pour précharger les images
const preloadImages = async (urls) => {
  const results = await Promise.all(urls.map(url => checkImg(url)));
  return results.every(result => result); // Vérifie si toutes les images sont chargées
};

  const responsive = {
    desktop: { breakpoint: { max: 3000, min: 1024 }, items: 4 },
    tablet: { breakpoint: { max: 1024, min: 768 }, items: 3 },
    mobile: { breakpoint: { max: 768, min: 320 }, items: 2 },
    mobileSmall: { breakpoint: { max: 320, min: 0 }, items: 1 },
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
      <h2>
        Derniers services
      </h2>
  
      <Suspense fallback={<div>Loading Carousel...</div>}>
  <Carousel responsive={responsive} infinite autoPlay={false}>
    {lastService.length > 0 ? (
      lastService.map((service, index) => {
        const imageUrl = service.picture;

        return (
          <div key={index} className="carousel-container-item">
            <Card>
              {imageUrl && (
                <div className="image-container">
                  <Image src={imageUrl} wrapped ui={false} />
                </div>
              )}
              <Card.Content className="card-content">
                <Card.Header>{service.title || 'Titre non disponible'}</Card.Header>
                <Card.Meta>
                  <span className="date">
                    Par {service.user ? `${service.user.firstName || 'Prénom inconnu'} ${service.user.lastName || 'Nom inconnu'}` : 'Utilisateur inconnu'}
                  </span>
                </Card.Meta>
                <Card.Description className="card-description">
                  {service.description ? (service.description.length > 150 ? `${service.description.substring(0, 150)}...` : service.description) : 'Description non disponible'}
                </Card.Description>
              </Card.Content>
            </Card>
          </div>
        );
      })
    ) : (
      <p>Aucun service disponible.</p>
    )}
  </Carousel>
</Suspense>

    </div>
  );
};
document.addEventListener("DOMContentLoaded", () => {
  const searchRoot = document.getElementById("bestservices-root");
  if (searchRoot) {
    const root = createRoot(searchRoot);
    root.render(<BestServicesCarousel />);
  }
});

export { BestServicesCarousel };
