#webcam-video {
	border: 1px solid black;
	width: 300px;
	height: 225px;
}

#webcam-video.has-photo {
	animation: snap 0.15s ease-out;
	-webkit-animation: snap 0.15s ease-out;
	-moz-animation: snap 0.15s ease-out;
}

@-webkit-keyframes snap {
    0% {-webkit-transform: scale(0.1, 0.1); opacity: 0.0;}
	100% {-webkit-transform: scale(1, 1); opacity: 1;}
}

@-moz-keyframes snap {
    0% {-moz-transform: scale(0.1, 0.1); opacity: 0.0;}
	100% {-moz-transform: scale(1, 1); opacity: 1;}
}

@keyframes snap {
    0% {transform: scale(0.1, 0.1); opacity: 0.0;}
	100% {transform: scale(1, 1); opacity: 1;}
}
