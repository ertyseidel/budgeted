/** @jsx React.DOM */
var PopUpBox = React.createClass({
	render: function() {
		return (
			<div class="popup-box-wrapper">
				<div class="popup-box">{this.props.children}</div>
			</div>
		);
	}
});
