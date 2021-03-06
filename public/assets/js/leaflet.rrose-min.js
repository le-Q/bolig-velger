L.Rrose = L.Popup.extend({
  _initLayout: function () {
    var t = "leaflet-rrose",
      i = this._container = L.DomUtil.create("div", t + " " + this.options.className + " leaflet-zoom-animated"),
      o, n;
    this.options.closeButton && ((o = this._closeButton = L.DomUtil.create("a", t + "-close-button", i)).href = "#close", o.innerHTML = "&#215;", L.DomEvent.on(o, "click", this._onCloseButtonClick, this));
    var e = 80,
      s = 80;
    this.options.position = "n";
    var a = s - this._map.latLngToContainerPoint(this._latlng).y;
    console.log(a), 0 < a && (this.options.position = "s");
    var p = this._map.latLngToContainerPoint(this._latlng).x - (this._map.getSize().x - e);
    0 < p ? this.options.position += "w" : 0 < (p = e - this._map.latLngToContainerPoint(this._latlng).x) && (this.options.position += "e"), /s/.test(this.options.position) ? (n = "s" === this.options.position ? (this._tipContainer = L.DomUtil.create("div", t + "-tip-container", i), this._wrapper = L.DomUtil.create("div", t + "-content-wrapper", i)) : (this._tipContainer = L.DomUtil.create("div", t + "-tip-container " + t + "-tip-container-" + this.options.position, i), this._wrapper = L.DomUtil.create("div", t + "-content-wrapper " + t + "-content-wrapper-" + this.options.position, i)), this._tip = L.DomUtil.create("div", t + "-tip " + t + "-tip-" + this.options.position, this._tipContainer), L.DomEvent.disableClickPropagation(n), this._contentNode = L.DomUtil.create("div", t + "-content", n), L.DomEvent.on(this._contentNode, "mousewheel", L.DomEvent.stopPropagation)) : ("n" === this.options.position ? (n = this._wrapper = L.DomUtil.create("div", t + "-content-wrapper", i), this._tipContainer = L.DomUtil.create("div", t + "-tip-container", i)) : (n = this._wrapper = L.DomUtil.create("div", t + "-content-wrapper " + t + "-content-wrapper-" + this.options.position, i), this._tipContainer = L.DomUtil.create("div", t + "-tip-container " + t + "-tip-container-" + this.options.position, i)), L.DomEvent.disableClickPropagation(n), this._contentNode = L.DomUtil.create("div", t + "-content", n), L.DomEvent.on(this._contentNode, "mousewheel", L.DomEvent.stopPropagation), this._tip = L.DomUtil.create("div", t + "-tip " + t + "-tip-" + this.options.position, this._tipContainer))
  },
  _updatePosition: function () {
    var t = this._map.latLngToLayerPoint(this._latlng),
      i = L.Browser.any3d,
      o = this.options.offset;
    i && L.DomUtil.setPosition(this._container, t), /s/.test(this.options.position) ? this._containerBottom = -this._container.offsetHeight + o.y - (i ? 0 : t.y) : this._containerBottom = -o.y - (i ? 0 : t.y), /e/.test(this.options.position) ? this._containerLeft = o.x + (i ? 0 : t.x) : /w/.test(this.options.position) ? this._containerLeft = -Math.round(this._containerWidth) + o.x + (i ? 0 : t.x) : this._containerLeft = -Math.round(this._containerWidth / 2) + o.x + (i ? 0 : t.x), this._container.style.bottom = this._containerBottom + "px", this._container.style.left = this._containerLeft + "px"
  }
});