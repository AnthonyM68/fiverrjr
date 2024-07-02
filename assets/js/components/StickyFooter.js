// Importation des modules nécessaires
//import React from 'react'; // Importer React
//import { Sticky } from 'semantic-ui-react'; // Importer le composant Sticky de Semantic UI React

// Fonction pour tester la compatibilité du positionnement sticky
/*const testSticky = () => {
  // Préfixes pour les différents navigateurs
  const prefix = ['', '-o-', '-webkit-', '-moz-', '-ms-'];
  // Accéder au style de la balise head
  const test = document.head.style;
  // Boucler à travers les préfixes pour tester la compatibilité
  for (let i = 0; i < prefix.length; i += 1) {
    test.position = `${prefix[i]}sticky`;
  }
  // Retourner true si la position sticky est supportée, false sinon
  return test.position === 'sticky';
};

// Classe MySticky qui étend la fonctionnalité du composant Sticky de Semantic UI React
class MySticky extends Sticky {
  constructor(props) {
    super(props);

    // Sauvegarder les méthodes originales
    const oldAddListeners = this.addListeners;
    const oldRemoveListeners = this.removeListeners;
    let myObserver;

    // Redéfinir la méthode addListeners pour ajouter un ResizeObserver
    this.addListeners = args => {
      oldAddListeners(args);

      myObserver = new ResizeObserver(entries => {
        entries.forEach(entry => {
          const { scrollContext } = props;

          if (scrollContext) {
            this.handleUpdate({ target: scrollContext });
          }
        });
      });

      myObserver.observe(this.props.context);
    };

    // Redéfinir la méthode removeListeners pour retirer le ResizeObserver
    this.removeListeners = args => {
      oldRemoveListeners(args);

      myObserver.unobserve(this.props.context);
    };
  }

  // Méthode pour mettre à jour la position du Sticky
  update = (e) => {
    this.ticking = false;
    this.assignRects();

    return this.didTouchScreenBottom() ? this.stickToScreenBottom(e) : this.stickToContextBottom(e);
  }
}

// Composant StickyFooter pour gérer le footer sticky
class StickyFooter extends React.Component {
  state = {
    supportsSticky: true, // État initial pour vérifier le support de sticky
  };

  // Vérifier le support de sticky lors du montage du composant
  componentDidMount() {
    const supportsSticky = testSticky();

    this.setState({
      supportsSticky,
    });
  }

  // Méthode pour rendre le footer
  renderFooter = () => {
    const { supportsSticky } = this.state;

    return (
      <footer className={supportsSticky ? 'sticky' : null}>
         //<h1>Component REACT</h1> 
      </footer>
    );
  };

  // Méthode render principale
  render() {
    const { supportsSticky } = this.state;

    return (
      supportsSticky ? this.renderFooter() :
      (
        <MySticky 
          context={this.props.context}
        >
          {this.renderFooter()}
        </MySticky>
      )
    );
  }
}

// Composant principal StickyParent
class StickyParent extends React.Component {
  state = {};

  // Référence du contexte (élément HTML)
  handleContextRef = contextRef => {
    this.setState({ contextRef });
  };

  // Méthode pour raccourcir la hauteur du contexte
  shortenContext = () => {
    const { contextRef } = this.state;

    if (contextRef.offsetHeight <= 200) return;

    contextRef.style.height = `${contextRef.offsetHeight - 200}px`;
  };

  // Méthode pour réinitialiser la hauteur du contexte
  resetContextHeight = () => {
    const { contextRef } = this.state;

    contextRef.style.height = '1000px';
  };

  // Méthode render principale
  render() {
    const { contextRef } = this.state;

    return (
      <div className="container">
        <div className="context" ref={this.handleContextRef}>
          <h1>Assets </h1>
          <ul>
        <li>Thême Jquery-ui:
            './node_modules/jquery-ui/dist/themes/Smoothness/theme.css' (smoothness)</li>
        <li>Thême Semantic:
            './semantic/src/themes/theme.less' (Github)</li>
        <li>Styles github:
            './semantic/src/themes/github/globals/site.variables'</li>
        <li>Semantic Globals:
            './semantic/src/site/globals/site.overrides'</li>
    </ul>
          //<button onClick={this.shortenContext}>Yes</button>
          //<button onClick={this.resetContextHeight}>No</button> 
        </div>
        <StickyFooter context={contextRef} />
        <footer>
          // <h1>Footer</h1> 
        </footer>
      </div>
    );
  }
}

export default StickyParent;*/
