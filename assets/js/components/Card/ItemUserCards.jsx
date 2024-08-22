import React from 'react';
import './../../../styles/cards/itemUserCards.scss';


import {
  ItemMeta,
  ItemImage,
  ItemHeader,
  ItemGroup,
  ItemExtra,
  ItemDescription,
  ItemContent,
  Image,
  Item,
} from 'semantic-ui-react'

const ItemUserCards = ({ items }) => (
  <Item.Group>
    {items.map((item, index) => (
      <Item key={index}>
        {item.picture && (
          <Item.Image
            size='tiny'
            src={item.picture}
            wrapped
            ui={true}
            className="itemUserCard-image"
            alt={`Image de profil de ${item.firstName} ${item.lastName}`}
          />
        )}

        <Item.Content className="itemUserCard-content">
          <Item.Header as='a' className="itemUserCard-header">
            {item.firstName} {item.lastName}
          </Item.Header>
          <Item.Meta className="itemUserCard-meta">{item.dateRegister}</Item.Meta>
          <Item.Description className="itemUserCard-description">{item.bio}</Item.Description>
          <Item.Extra className="itemUserCard-extra">{item.city}</Item.Extra>
        </Item.Content>
      </Item>
    ))}
  </Item.Group>
);

export default ItemUserCards;