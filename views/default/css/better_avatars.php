#webcam-video {
	border: 1px solid black;
	width: 480px;
	height: 360px;
}

#webcam-video.has-photo {
	animation: pulsate 0.5s ease-out;
	-webkit-animation: pulsate 0.5s ease-out;
	-moz-animation: pulsate 0.5s ease-out;
}

@-webkit-keyframes pulsate {
    0% {-webkit-transform: scale(0.1, 0.1); opacity: 0.0;}
    100% {opacity: 1.0;}
}

@-moz-keyframes pulsate {
    0% {-moz-transform: scale(0.1, 0.1); opacity: 0.0;}
    100% {opacity: 1.0;}
}

@keyframes pulsate {
    0% {transform: scale(0.1, 0.1); opacity: 0.0;}
    100% {opacity: 1.0;}
}