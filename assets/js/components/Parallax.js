import React from 'react';

const Parallax = () => {
    return (
        <div className="parallax-container">
            <div className="parallax-content" >
                <div className="parallax parallax-home"></div>
                <div className="floating-text">
                    <h1>Explorez et trouvez un service</h1>
                    <p>Effet visuel captivant pour vos projets web</p>
                </div>
            </div>
        </div>
    );


};


const ParallaxDouble = () => {
    return (
        <div className="parallax-container">
            <div className="parallax-content">
                <div className="parallax"></div>
                <div className="floating-text">
                    <h1>Explorez et trouvez un service</h1>
                    <p>Effet visuel captivant pour vos projets web</p>
                </div>
                <div className="parallax"></div>
            </div>
        </div>
    );
};
export { Parallax, ParallaxDouble };