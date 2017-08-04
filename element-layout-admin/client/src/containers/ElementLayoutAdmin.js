import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators, compose } from 'redux';
import SilverStripeComponent from 'lib/SilverStripeComponent';
import backend from 'lib/Backend';
import i18n from 'i18n';
import * as galleryActions from 'state/gallery/GalleryActions';
import * as breadcrumbsActions from 'state/breadcrumbs/BreadcrumbsActions';
import * as queuedFilesActions from 'state/queuedFiles/QueuedFilesActions';
import Editor from 'containers/Editor/Editor';
import Gallery from 'containers/Gallery/Gallery';
import Breadcrumb from 'components/Breadcrumb/Breadcrumb';
import Toolbar from 'components/Toolbar/Toolbar';
import { withApollo } from 'react-apollo';
import Search, { hasFilters } from 'components/Search/Search';
import readFilesQuery from 'state/files/readFilesQuery';
import deleteFileMutation from 'state/files/deleteFileMutation';
import unpublishFileMutation from 'state/files/unpublishFileMutation';
import CONSTANTS from 'constants/index';

function getFormSchema({ config, viewAction, folderId, fileId, type }) {
  let schemaUrl = null;
  let targetId = null;

  return { config.form.fileEditForm.schemaUrl, targetId };
}

class ElementLayoutAdmin extends SilverStripeComponent {

  constructor(props) {
    super(props);
    this.handleOpenFile = this.handleOpenFile.bind(this);
    this.handleCloseFile = this.handleCloseFile.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleUnpublish = this.handleUnpublish.bind(this);
    this.handleDoSearch = this.handleDoSearch.bind(this);
    this.handleSubmitEditor = this.handleSubmitEditor.bind(this);
    this.handleOpenFolder = this.handleOpenFolder.bind(this);
    this.handleSort = this.handleSort.bind(this);
    this.handleSetPage = this.handleSetPage.bind(this);
    this.createEndpoint = this.createEndpoint.bind(this);
    this.handleBackButtonClick = this.handleBackButtonClick.bind(this);
    this.handleFolderIcon = this.handleFolderIcon.bind(this);
    this.handleBrowse = this.handleBrowse.bind(this);
    this.handleViewChange = this.handleViewChange.bind(this);
    this.handleUpload = this.handleUpload.bind(this);
    this.handleCreateFolder = this.handleCreateFolder.bind(this);
    this.handleMoveFilesSuccess = this.handleMoveFilesSuccess.bind(this);
    this.compare = this.compare.bind(this);
    this.setBreadcrumbs = this.setBreadcrumbs.bind(this);
  }


  render() {
    return (
        <div className="flexbox-area-grow fill-width fill-height gallery">
          {this.renderGallery()}
          {this.renderEditor()}
        </div>
        {this.props.type !== 'admin' && this.props.loading &&
        [<div key="overlay" className="cms-content-loading-overlay ui-widget-overlay-light"></div>,
        <div key="spinner" className="cms-content-loading-spinner"></div>]
        }
      </div>
    );
  }
}


function mapStateToProps(state) {
  return {
    securityId: state.config.SecurityID,
    // TODO Refactor "queued files" into separate visual area and remove coupling here
    queuedFiles: state.assetAdmin.queuedFiles,
  };
}

function mapDispatchToProps(dispatch) {
  return {
    actions: {
      gallery: bindActionCreators(galleryActions, dispatch),
      breadcrumbsActions: bindActionCreators(breadcrumbsActions, dispatch),
      // TODO Refactor "queued files" into separate visual area and remove coupling here
      queuedFiles: bindActionCreators(queuedFilesActions, dispatch),
    },
  };
}

export { ElementLayoutAdmin, getFormSchema };

export default compose(
  connect(mapStateToProps, mapDispatchToProps),
  readFilesQuery,
  deleteFileMutation,
  unpublishFileMutation,
  (component) => withApollo(component)
)(ElementLayoutAdmin);
