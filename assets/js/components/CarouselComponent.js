
import React from 'react';
import { Carousel } from 'react-responsive-carousel';

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

export default CarouselComponent;
