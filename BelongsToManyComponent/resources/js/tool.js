import Tool from './components/Tool'

Nova.booting((app, store) => {
  app.component('belongs-to-many-component', Tool)
})
