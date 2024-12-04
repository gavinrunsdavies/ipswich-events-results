<div
  id="myGrid"
  style="width: 100%; height: 100%"
  class="ag-theme-quartz"></div>
<script>
  (function() {
    const appLocation = "";

    window.__basePath = appLocation;
  })();
</script>
<script>
  var appLocation = "";
  var boilerplatePath = "";
  var systemJsMap = {
    "@ag-grid-community/styles": "https://cdn.jsdelivr.net/npm/@ag-grid-community/styles@32.0.2",
    "ag-grid-charts-enterprise": "https://cdn.jsdelivr.net/npm/ag-grid-charts-enterprise@32.0.2/",
    "ag-grid-community": "https://cdn.jsdelivr.net/npm/ag-grid-community@32.0.2",
    "ag-grid-enterprise": "https://cdn.jsdelivr.net/npm/ag-grid-enterprise@32.0.2/",
  };
  var systemJsPaths = {
    "@ag-grid-community/client-side-row-model": "https://cdn.jsdelivr.net/npm/@ag-grid-community/client-side-row-model@32.0.2/dist/package/main.cjs.js",
    "@ag-grid-community/core": "https://cdn.jsdelivr.net/npm/@ag-grid-community/core@32.0.2/dist/package/main.cjs.js",
    "@ag-grid-community/csv-export": "https://cdn.jsdelivr.net/npm/@ag-grid-community/csv-export@32.0.2/dist/csv-export.cjs.min.js",
    "@ag-grid-community/infinite-row-model": "https://cdn.jsdelivr.net/npm/@ag-grid-community/infinite-row-model@32.0.2/dist/package/main.cjs.js",
    "@ag-grid-community/locale": "https://cdn.jsdelivr.net/npm/@ag-grid-community/locale@32.0.2/dist/package/main.cjs.js",
    "ag-charts-community": "https://cdn.jsdelivr.net/npm/ag-charts-community@10.0.2/",
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/systemjs@0.19.47/dist/system.js"></script>
<script src="systemjs.config.js"></script>
<script>
  System.import("main.ts").catch(function(err) {
    document.body.innerHTML =
      '<div class="example-error" style="background:#fdb022;padding:1rem;">' +
      "Example Error: " +
      err +
      "</div>";
    console.error(err);
  });
</script>