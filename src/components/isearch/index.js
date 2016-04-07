import React, { PropTypes, Component } from 'react';
import SearchBar from '../../../lib/search-bar/';
import SearchSummary from '../../../lib/search-summary/';
import Tags from '../../../lib/tags/';
import SearchResults from '../search-results';

class ISearch extends Component {

  constructor () {
    super();
    this.fetchQueryResults = this.fetchQueryResults.bind(this);
  }

  componentWillMount () {
    this.fetchQueryResults();
  }

  /**
   * For testing and building purposes we pass through a list of fixed tags.
   * TODO: Replace this with the proper solution!
   */

  fetchQueryResults () {
    this.props.fetchQuerySearchResults(12345, 1, 20);
  }

  handleOnButtonClick () {
    console.log('clicked button');
  }

  render () {
    const {
      tags,
      items,
      onYesFilter,
      onFilterClick,
      showAddMessage,
      filterVisibleState,
      removeTag
    } = this.props;

    return (
      <section className='container'>
        <SearchBar onButtonClick={this.handleOnButtonClick} />
        <SearchSummary
          city='Bodrum'
          country='Turkey'
          durationInWeeks={2}
          paxMix='2 adults, 2 children'
          departureDate='Sun 13 jul 2016'
          returnDate='Tue 15 jul 2016'
        />
        <Tags tags={tags} removeTag={removeTag} />
        <SearchResults
          items={items}
          onYesFilter={onYesFilter}
          onFilterClick={onFilterClick}
          filterVisibleState={filterVisibleState}
          showAddMessage={showAddMessage}
        />
      </section>
    );
  }
}

ISearch.propTypes = {
  tags: PropTypes.array,
  items: PropTypes.array,
  onYesFilter: PropTypes.func,
  onFilterClick: PropTypes.func,
  showAddMessage: PropTypes.func,
  hideAddMessage: PropTypes.func,
  filterVisibleState: PropTypes.object,
  fetchQuerySearchResults: PropTypes.func,
  removeTag: PropTypes.func
};

export default ISearch;
