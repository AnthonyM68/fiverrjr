import React from 'react';
import { Sticky } from 'semantic-ui-react';

// Test de compatibilité de la position sticky
const testSticky = () => {
  console.log('-> StickyFooter.js loaded');
  const prefix = ['', '-o-', '-webkit-', '-moz-', '-ms-'];
  const test = document.createElement('div');
  for (let i = 0; i < prefix.length; i += 1) {
    test.style.position = `${prefix[i]}sticky`;
  }
  return test.style.position.includes('sticky');
};

// Extension de Sticky pour inclure un ResizeObserver
class MySticky extends Sticky {
  constructor(props) {
    super(props);
    const oldAddListeners = this.addListeners;
    const oldRemoveListeners = this.removeListeners;
    let myObserver;

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

    this.removeListeners = args => {
      oldRemoveListeners(args);
      myObserver.unobserve(this.props.context);
    };
  }

  update = (e) => {
    this.ticking = false;
    this.assignRects();
    return this.didTouchScreenBottom() ? this.stickToScreenBottom(e) : this.stickToContextBottom(e);
  }
}

// Composant StickyFooter
class StickyFooter extends React.Component {
  state = { supportsSticky: true };

  componentDidMount() {
    const supportsSticky = testSticky();
    this.setState({ supportsSticky });
  }

  renderFooter = () => {
    const { supportsSticky } = this.state;
    return (
      <footer className={supportsSticky ? 'sticky' : null}>
        <h1>Component REACT</h1>
      </footer>
    );
  };

  render() {
    const { supportsSticky } = this.state;
    return (
      supportsSticky ? this.renderFooter() :
        <MySticky context={this.props.context}>{this.renderFooter()}</MySticky>
    );
  }
}

// Composant principal StickyParent
class StickyParent extends React.Component {
  state = {};

  handleContextRef = contextRef => {
    this.setState({ contextRef });
  };

  shortenContext = () => {
    const { contextRef } = this.state;
    if (contextRef.offsetHeight <= 200) return;
    contextRef.style.height = `${contextRef.offsetHeight - 200}px`;
  };

  resetContextHeight = () => {
    const { contextRef } = this.state;
    contextRef.style.height = '1000px';
  };

  render() {
    const { contextRef } = this.state;
    return (
      <div className="outer-container">
        <div className="inner-container" ref={this.handleContextRef}>
        <div className="context" ref={this.handleContextRef}>
          <h1>Assets</h1>
          <ul>
            <li>Thême Jquery-ui: './node_modules/jquery-ui/dist/themes/Smoothness/theme.css' (smoothness)</li>
            <li>Thême Semantic: './semantic/src/themes/theme.less' (Github)</li>
            <li>Styles github: './semantic/src/themes/github/globals/site.variables'</li>
            <li>Semantic Globals: './semantic/src/site/globals/site.overrides'</li>
          </ul>
        </div>
        <StickyFooter context={contextRef} />
      </div>
      </div>
    );
  }
}

export default StickyParent;
