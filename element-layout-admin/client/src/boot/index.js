import Injector from 'lib/Injector';
import ElementLayoutField from 'components/ElementLayoutField/ElementLayoutField';


document.addEventListener('DOMContentLoaded', () => {
  Injector.component.register('ElementLayoutField', ElementLayoutField);
});

// import { combineReducers } from 'redux';
// import Config from 'lib/Config';
// import reactRouteRegister from 'lib/ReactRouteRegister';
// import galleryReducer from 'state/gallery/GalleryReducer';
// import queuedFilesReducer from 'state/queuedFiles/QueuedFilesReducer';
// import AssetAdminRouter from 'containers/AssetAdmin/AssetAdminRouter';
// import uploadFieldReducer from 'state/uploadField/UploadFieldReducer';
// import previewFieldReducer from 'state/previewField/PreviewFieldReducer';
// import UploadField from 'components/UploadField/UploadField';
// import PreviewImageField from 'components/PreviewImageField/PreviewImageField';
// import ProportionConstraintField from 'components/ProportionConstraintField/ProportionConstraintField';
// import HistoryList from 'containers/HistoryList/HistoryList';

// document.addEventListener('DOMContentLoaded', () => {
//  Injector.component.register('ElementLayoutField', ElementLayoutField);

  // const sectionConfig = Config.getSection('SilverStripe\\AssetAdmin\\Controller\\AssetAdmin');

  // reactRouteRegister.add({
  //   path: sectionConfig.url,
  //   component: AssetAdminRouter,
  //   indexRoute: { component: AssetAdminRouter },
  //   childRoutes: [
  //     {
  //       path: 'show/:folderId/:viewAction/:fileId',
  //       component: AssetAdminRouter,
  //     },
  //     {
  //       path: 'show/:folderId/:viewAction',
  //       component: AssetAdminRouter,
  //     },
  //     {
  //       path: 'show/:folderId',
  //       component: AssetAdminRouter,
  //     },
  //   ],
  // });

  // Injector.reducer.register('assetAdmin', combineReducers({
  //   gallery: galleryReducer,
  //   queuedFiles: queuedFilesReducer,
  //   uploadField: uploadFieldReducer,
  //   previewField: previewFieldReducer,
  // }));
// });
