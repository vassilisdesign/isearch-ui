import React, { PropTypes, Component } from 'react';
import Header from '../../../lib/header/';
import SearchSummary from '../../../lib/search-summary';
import Tags from '../../../lib/tags/';
import SearchResults from '../search-results';
import LoadingSpinner from '../../../lib/spinner';
import SearchBar from '../../../lib/search';
import './style.css';

class ISearch extends Component {

  constructor () {
    super();
    this.state = {
      scrollY: 0,
      screenWidth: window.innerWidth
    };
    this.scrollToSavedPosition = this.scrollToSavedPosition.bind(this);
    this.handleResize = this.handleResize.bind(this);
  }

  componentWillMount () {
    this.props.addSingleTag('Top inspiration', 'marketing:homepage.dk.spies', true);
    window.addEventListener('resize', this.handleResize);
  }

  scrollToSavedPosition () {
    if (this.state.scrollY > 0) {
      window.scrollTo(0, this.state.scrollY);
    }
  }

  componentWillUnmount () {
    window.removeEventListener('resize', this.handleResize);
  }

  handleResize () {
    this.setState({ screenWidth: window.innerWidth });
  }

  renderResults () {
    const {
      displayedItems,
      onYesFilter,
      onFilterClick,
      filterVisibleState,
      setHotelPage,
      numberOfChildrenTitle,
      numberOfAdultsTitle,
      bucketId
    } = this.props;
    return (
      <SearchResults
        items={displayedItems}
        onYesFilter={onYesFilter}
        onFilterClick={onFilterClick}
        filterVisibleState={filterVisibleState}
        setHotelPage={setHotelPage}
        totalPassengers={Number(numberOfAdultsTitle) + Number(numberOfChildrenTitle)}
        bucketId={bucketId}
      />
    );
  }

  // componentDidUpdate (prevProps) {
  //   const pageChanged = (prevProps.hotelPage !== this.props.hotelPage) || (prevProps.articlePage !==
  // this.props.articlePage); const searchPage = !this.props.hotelPage && !this.props.articlePage; // current page is
  // search if (pageChanged && searchPage) { console.log('pageChanged', pageChanged, searchPage, this.state.scrollY);
  // this.scrollToSavedPosition(); } }
  render () {
    console.log('SCREEN WIDHT', window.innerWidth, this.state.screenWidth);
    const {
      tags,
      removeTag,
      setSearchString,
      startSearch,
      autocompleteError,
      autocompleteOptions,
      searchString,
      getAutocompleteOptions,
      inAutoCompleteSearch,
      addSingleTag,
      clearSearchString,
      error,
      loading,
      numberOfChildren,
      numberOfAdults,
      childAge1,
      childAge2,
      childAge3,
      childAge4,
      departureAirport,
      duration,
      departureDate,
      setNumberOfChildren,
      setNumberOfAdults,
      setChildAge,
      setDepartureAirport,
      setDuration,
      setDurationTitle,
      setNumberOfAdultsTitle,
      setNumberOfChildrenTitle,
      numberOfChildrenTitle,
      numberOfAdultsTitle,
      durationTitle,
      setDepartureDate
    } = this.props;
    return (
      <section>
        <SearchSummary
          numberOfChildren={numberOfChildren}
          numberOfAdults={numberOfAdults}
          setChildAge={setChildAge}
          childAge1={childAge1}
          childAge2={childAge2}
          childAge3={childAge3}
          childAge4={childAge4}
          departureAirport={departureAirport}
          duration={duration}
          departureDate={departureDate}
          setNumberOfChildren={setNumberOfChildren}
          setNumberOfAdults={setNumberOfAdults}
          setDepartureAirport={setDepartureAirport}
          setDuration={setDuration}
          setDurationTitle={setDurationTitle}
          setNumberOfAdultsTitle={setNumberOfAdultsTitle}
          setNumberOfChildrenTitle={setNumberOfChildrenTitle}
          numberOfAdultsTitle={numberOfAdultsTitle}
          numberOfChildrenTitle={numberOfChildrenTitle}
          durationTitle={durationTitle}
          setDepartureDate={setDepartureDate}
          startSearch={startSearch}
        />
        {
          this.state.screenWidth < 553 ? [
            <Header searchBar={false}/>,
            <SearchBar
              addSingleTag={addSingleTag}
              startSearch={startSearch}
              setSearchString={setSearchString}
              autocompleteError={autocompleteError}
              autocompleteOptions={autocompleteOptions}
              searchString={searchString}
              getAutocompleteOptions={getAutocompleteOptions}
              inAutoCompleteSearch={inAutoCompleteSearch}
              clearSearchString={clearSearchString}
            />
          ]
            : <Header
            addSingleTag={addSingleTag}
            startSearch={startSearch}
            setSearchString={setSearchString}
            autocompleteError={autocompleteError}
            autocompleteOptions={autocompleteOptions}
            searchString={searchString}
            getAutocompleteOptions={getAutocompleteOptions}
            inAutoCompleteSearch={inAutoCompleteSearch}
            clearSearchString={clearSearchString}
            searchBar
          />
        }
        <Tags
          tags={tags}
          removeTag={removeTag}
        />
        { loading &&
        <div className='spinnerContainer'>
          <LoadingSpinner/>
        </div>
        }
        { error && <div className='errorMessage'>{error}</div> }
        { this.renderResults() }
      </section>
    );
  }
}

ISearch.propTypes = {
  bucketId: PropTypes.string,
  // for random initial results
  fetchQuerySearchResults: PropTypes.func,
  getArticle: PropTypes.func,
  // results
  loading: PropTypes.bool,
  error: PropTypes.string,
  displayedItems: PropTypes.array,
  onYesFilter: PropTypes.func,
  onFilterClick: PropTypes.func,
  filterVisibleState: PropTypes.object,

  // autocomplete
  autocompleteError: PropTypes.string,
  autocompleteOptions: PropTypes.array,
  inAutoCompleteSearch: PropTypes.bool,
  getAutocompleteOptions: PropTypes.func,

  // search bar
  clearSearchString: PropTypes.func,
  setSearchString: PropTypes.func,
  searchString: PropTypes.string,
  startSearch: PropTypes.func,
  viewArticle: PropTypes.func,
  onAddArticleTag: PropTypes.func,
  addSearchStringTag: PropTypes.func,

  // tags
  tags: PropTypes.array,
  addTag: PropTypes.func,
  addSingleTag: PropTypes.func,
  removeTag: PropTypes.func,
  setHotelPage: PropTypes.func,
  hotelInView: PropTypes.object,

  // travel info
  setNumberOfChildren: PropTypes.func,
  setNumberOfAdults: PropTypes.func,
  setDepartureAirport: PropTypes.func,
  setDuration: PropTypes.func,
  numberOfChildren: PropTypes.string,
  numberOfAdults: PropTypes.string,
  childAge1: PropTypes.string,
  childAge2: PropTypes.string,
  childAge3: PropTypes.string,
  childAge4: PropTypes.string,
  departureAirport: PropTypes.string,
  duration: PropTypes.string,
  departureDate: PropTypes.string,
  setChildAge: PropTypes.func,
  setNumberOfAdultsTitle: PropTypes.func,
  setNumberOfChildrenTitle: PropTypes.func,
  setDurationTitle: PropTypes.func,
  numberOfAdultsTitle: PropTypes.string,
  numberOfChildrenTitle: PropTypes.string,
  durationTitle: PropTypes.string,
  setDepartureDate: PropTypes.func

  // showAddMessage: PropTypes.func,
  // hideAddMessage: PropTypes.func,
};

export default ISearch;
