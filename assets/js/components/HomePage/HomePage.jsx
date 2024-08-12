import React, { useState, useEffect } from "react";
import { createRoot } from "react-dom/client";

import {
  Container,
  Grid,
  Card,
  Image,
  Header,
  Segment,
} from "semantic-ui-react";

import { useInView } from "react-intersection-observer";

const HomePage = () => {
  // État initial basé sur les données injectées par Twig
  const [lastDeveloper, setLastDeveloper] = useState(null);
  const [lastClient, setLastClient] = useState(null);
  const [loading, setLoading] = useState(true);

  // Utilisez useEffect pour initialiser les états
  useEffect(() => {
    if (window.__INITIAL_DATA__) {
      console.log(
        "Last Developer Data:",
        window.__INITIAL_DATA__.lastDeveloper
      );
      console.log("Last Client Data:", window.__INITIAL_DATA__.lastClient);

      setLastDeveloper(window.__INITIAL_DATA__.lastDeveloper || null);
      setLastClient(window.__INITIAL_DATA__.lastClient || null);
      setLoading(false);
    } else {
      console.error("window.__INITIAL_DATA__ is not defined");
      setLoading(true);
    }
  }, []);

  // Observateurs pour les cartes
  const [refDeveloper, inViewDeveloper] = useInView({
    triggerOnce: true,
    threshold: 0.5,
  });
  const [refDeveloperWelcom, inViewDeveloperWelcom] = useInView({
    triggerOnce: true,
    threshold: 0.5,
  });
  const [refClient, inViewClient] = useInView({
    triggerOnce: true,
    threshold: 0.5,
  });
  const [refClientWelcom, inViewClientWelcom] = useInView({
    triggerOnce: true,
    threshold: 0.5,
  });
  const [refTitle, inViewTitle] = useInView({
    triggerOnce: true,
    threshold: 0.5,
  });

  if (loading) {
    return <div className="homepage-ui active inline loader"></div>;
  }

  return (
    <div className="homepage-container">
      <div className="homepage-overlay"></div>
      <Grid stackable padded="vertically" className="homepage-home">
        {/* Developer Section */}
        <Grid.Row columns={2} verticalAlign="middle">
          <Grid.Column>
            <div ref={refDeveloper}>
              <Card
                centered
                className={`${
                  inViewDeveloper ? "homepage-uk-animation-scale-up slow" : ""
                }`}
              >
                <Card.Content>
                  <Card.Header>
                    {lastDeveloper.firstName} {lastDeveloper.lastName}
                  </Card.Header>

                  <Card.Meta>
                    <span>
                      Date d'inscription:{" "}
                      {new Date(
                        lastDeveloper.dateRegister
                      ).toLocaleDateString()}
                    </span>
                  </Card.Meta>

                  {lastDeveloper.bio && (
                    <Card.Description>
                      {lastDeveloper.bio.slice(0, 150)}...
                    </Card.Description>
                  )}
                </Card.Content>

                <Card.Content extra>
                  <Image
                    avatar
                    src={lastDeveloper.picture || "/uploads/default-avatar.png"}
                    alt={lastDeveloper.username || "Default alt text"}
                  />
                  {lastDeveloper.roles &&
                    lastDeveloper.roles.includes("ROLE_DEVELOPER") && (
                      <span className="homepage-category">Développeur</span>
                    )}
                </Card.Content>
              </Card>
            </div>
          </Grid.Column>

          <Grid.Column>
            <div
              ref={refDeveloperWelcom}
              className={`${
                inViewDeveloperWelcom ? "homepage-uk-animation-slide-right slow" : ""
              }`}
            >
              <Segment>
                <Header as="h2">{lastDeveloper.username}</Header>
                <p>
                  Nous sommes ravis d'accueillir {lastDeveloper.username}, notre
                  nouveau développeur sur Fiverrjr ! Expert en développement web
                  et mobile, {lastDeveloper.firstName} maîtrise HTML, CSS,
                  JavaScript, React et Node.js.
                </p>
                <p>
                  Découvrez son <a href="">profil</a> et ses projets pour voir
                  comment il peut apporter des solutions innovantes à vos
                  besoins. Bienvenue, {lastDeveloper.firstName} !
                </p>
              </Segment>
            </div>
          </Grid.Column>
        </Grid.Row>

        {/* Contact Section */}
        <Grid.Row centered>
          <Grid.Column textAlign="center">
            <div
              ref={refTitle}
              className={`${
                inViewTitle
                  ? "homepage-uk-animation-scale-up homepage-uk-transform-origin-top-center homepage-custom-margin homepage-custom-padding"
                  : ""
              }`}
            >
              <Header as="h1" className="homepage-ui header">
                Contactez des Développeurs Front-End maintenant
              </Header>
              <p className="homepage-ui paragraph homepage-margined homepage-padded">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim
                omnis doloribus quo deserunt optio! Tempora fugiat, harum ipsa
                alias magnam neque! Maiores dolores molestias magnam ipsum at
              </p>
            </div>
          </Grid.Column>
        </Grid.Row>

        {/* Client Section */}
        <Grid.Row columns={2} verticalAlign="middle">
          <Grid.Column>
            <div
              ref={refClientWelcom}
              className={`${
                inViewClientWelcom ? "homepage-uk-animation-slide-left slow" : ""
              }`}
            >
              <Segment>
                <Header as="h2">{lastClient.username}</Header>
                <p>
                  Nous sommes ravis d'annoncer l'arrivée de{" "}
                  {lastClient.firstName} {lastClient.lastName} sur notre
                  plateforme ! En tant qu'entreprise innovante et en pleine
                  croissance, {lastClient.username} est en quête de jeunes
                  développeurs talentueux pour renforcer leur équipe dynamique.
                </p>
                <p>
                  Ils offrent des opportunités passionnantes dans le
                  développement web et mobile, avec un accent sur des
                  technologies telles que HTML, CSS, JavaScript, React et
                  Node.js.
                </p>
              </Segment>
            </div>
          </Grid.Column>
          <Grid.Column>
            <div ref={refClient}>
              <Card
                centered
                className={`${
                  inViewClient ? "homepage-uk-animation-scale-up slow" : ""
                }`}
              >
                <Card.Content>
                  <Card.Header>
                    {lastClient.firstName} {lastClient.lastName}
                  </Card.Header>
                  <Card.Meta>
                    <span>
                      Date d'inscription:{" "}
                      {new Date(lastClient.dateRegister).toLocaleDateString()}
                    </span>
                  </Card.Meta>
                  {lastClient.bio && (
                    <Card.Description>
                      {lastClient.bio.slice(0, 150)}...
                    </Card.Description>
                  )}
                </Card.Content>
                <Card.Content extra>
                  <Image
                    avatar
                    src={lastClient.picture || "/uploads/default-avatar.png"}
                    alt={lastClient.username || "Default alt text"}
                  />
                  {lastClient.roles &&
                    lastClient.roles.includes("ROLE_CLIENT") && (
                      <span className="homepage-category">Client</span>
                    )}
                </Card.Content>
              </Card>
            </div>
          </Grid.Column>
        </Grid.Row>
      </Grid>
    </div>
  );
};

export { HomePage };
