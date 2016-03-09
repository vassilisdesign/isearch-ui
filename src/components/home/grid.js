'use strict';

import React, { Component, PropTypes } from 'react';
import Masonry from 'react-masonry-component';
import FilterTile from 'filter-tile';
import PackageTile from 'package-tile';
import Article from 'article';

require('./style.css');

const masonryOptions = {
  transitionDuration: 0,
  itemSelector: '.gridItem',
  columnWidth: '.gridSizer',
  fitWidth: true,
  gutter: 14 // horizontal spacing between tiles
};

class TileGrid extends Component {
  render () {
    const {
      filter1,
      filter2,
      filter3,
      filter4,
      article1,
      article2,
      package1
    } = this.props.tileData;

    return (
      <Masonry
        elementType={'div'}
        options={masonryOptions}
        disableImagesLoaded={false}
        className='grid'
      >
        <div className='gridSizer'/>
        <div className='gridItem'>
          <PackageTile {...package1} />
        </div>
        <div className='gridItem'>
          <FilterTile description={filter1} color={'#B9CAA8'}/>
        </div>
        <div className='gridItem'>
          <Article {...article1} />
        </div>
        <div className='gridItem'>
          <FilterTile description={filter4} color={'#8FB8C3'}/>
        </div>
        <div className='gridItem'>
          <PackageTile {...package1} />
        </div>
        <div className='gridItem'>
          <FilterTile description={filter2} color={'#F19024'}/>
        </div>
        <div className='gridItem'>
          <Article {...article2} />
        </div>
        <div className='gridItem'>
          <FilterTile description={filter3} color={'#DA3A68'}/>
        </div>
      </Masonry>
    );
  }
}

TileGrid.propTypes = {
  tileData: PropTypes.obj
};

export default TileGrid;
