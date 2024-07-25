import React, { useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import 'semantic-ui-css/semantic.min.css';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import $ from 'jquery';
import 'slick-carousel';

const ParallaxComponent = ({id}) => {
    return <Parallax id={id} />;
};
const Parallax = ({ id }) => {
    useEffect(() => {
        // Initialisation du carousel
        $('.ad-carousel').slick({
            dots: false,
            infinite: true,
            speed: 500,
            slidesToShow: 6,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: false,
        });
    }, []);

    return (
        <div className="parallax-container">
            <div className="parallax-content">
                <div className={`parallax parallax-home-${id}`}></div>
                <div className="ad-banner uk-animation-fade">
                    <div className="custom-container">
                    <div className="ad-carousel">
                            <div>
                                <a href="https://example.com/codealsace" target="_blank">CodeAlsace</a>
                            </div>
                            <div>
                                <a href="https://example.com/techhautrhin" target="_blank">TechHautRhin</a>
                            </div>
                            <div>
                                <a href="https://example.com/innostart-alsace" target="_blank">InnoStart Alsace</a>
                            </div>
                            <div>
                                <a href="https://example.com/devfactory-hautrhin" target="_blank">DevFactory Haut-Rhin</a>
                            </div>
                            <div>
                                <a href="https://example.com/alsace-digital-academy" target="_blank">Alsace Digital Academy</a>
                            </div>
                            <div>
                                <a href="https://example.com/starthub68" target="_blank">StartHub 68</a>
                            </div>
                            <div>
                                <a href="https://example.com/innovit-hautrhin" target="_blank">InnovIT Haut-Rhin</a>
                            </div>
                        </div>
                        <h2 className="ui center aligned icon header">
                        <img src="/img/services/alsace.png" alt="Friends Icon" className="circular icon" />
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    );
};

export { Parallax };




const parallaxRoot = document.getElementById('parallax-root');
if (parallaxRoot) {
    const root = createRoot(parallaxRoot);
    root.render(<ParallaxComponent id="1"/>);
}

const parallaxRoot2 = document.getElementById('parallax-root-2');
if (parallaxRoot2) {
    const root = createRoot(parallaxRoot2);
    root.render(<ParallaxComponent id="2"/>);
}