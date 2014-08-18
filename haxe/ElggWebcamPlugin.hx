import flash.events.MouseEvent;
import flash.Lib;
import flash.display.Bitmap;
import flash.display.BitmapData;
import flash.display.MovieClip;
import flash.media.Video;
import flash.media.Camera;
import flash.events.StatusEvent;
import flash.external.ExternalInterface;
import flash.media.Sound;
import flash.net.URLRequest;

import flash.geom.Rectangle;
import flash.utils.ByteArray;

import haxe.crypto.Base64;

import flash.text.TextField;
import flash.text.TextFieldAutoSize;

import feffects.Tween;

class ElggWebcamPlugin {
	public static function main() {
		var cam = new Webcam();
	}
}

class Webcam {
	var mc:MovieClip;
	var vid:Video;
	var cam:Camera = Camera.getCamera();
	var vidContainer:MovieClip;
	var hasSnap:Bool = false;
	var videoWidth = Lib.current.stage.stageWidth;
	var videoHeight = Lib.current.stage.stageHeight;
	var siteURL = ExternalInterface.call('elgg.get_site_url');
	var shutterSound:Sound;

	public function new() {
		if (cam != null) {
			cam.addEventListener(StatusEvent.STATUS, camInit);
			cam.setMode(320, 240, 30);
			cam.setQuality(0, 100);

			mc = Lib.current;
			vid = new Video(cam.width, cam.height);
			vid.attachCamera(cam);

			// have to have a container to attach a click event.
			vidContainer = new MovieClip();
			vidContainer.addChild(vid);
			vidContainer.width = videoWidth;
			vidContainer.height = videoHeight;

			mc.addChild(vidContainer);

			shutterSound = new Sound(new URLRequest(siteURL + "/mod/webcam/haxe/shutter.mp3"));

			// pause / unpause on click
			vidContainer.addEventListener(MouseEvent.CLICK, function(e:MouseEvent) {
				if (!hasSnap) {
					shutterSound.play();
					var bitmapData: BitmapData = new BitmapData(cam.width, cam.height);
					var bitmap: Bitmap = new Bitmap(bitmapData);

					// place over playing video
					bitmap.x = 0;
					bitmap.y = 0;
					bitmap.width = videoWidth;
					bitmap.height = videoHeight;
					bitmap.name = "snap";

					mc.addChild(bitmap);

					bitmapData.draw(vid);
					
					hasSnap = true;

					var byteArray:ByteArray = new ByteArray();
					byteArray = bitmapData.encode(new Rectangle(0, 0, cam.width, cam.height), new flash.display.JPEGEncoderOptions()); 
					var base64 = Base64.encode(haxe.io.Bytes.ofData(byteArray));
					ExternalInterface.call('elgg.avatar.saveBase64Input', base64, '.elgg-form-avatar-upload');
					ExternalInterface.call('elgg.avatar.saveBase64Input', base64, '.elgg-form-register');
				} else {
					mc.removeChild(mc.getChildByName("snap"));
					ExternalInterface.call('elgg.avatar.removeBase64Input');
					hasSnap = false;
				}
			});
		} else {
			var text = new TextField();
			text.text = "No cameras found.";
			text.autoSize = TextFieldAutoSize.LEFT;
			mc = Lib.current;
			mc.addChild(text);
		}
	}

	public function camInit(event:StatusEvent) {
		if (event.code == "Camera.Muted") {
			var text = new TextField();
			text.text = "Permission to access camera was denied.";
			text.autoSize = TextFieldAutoSize.LEFT;
			mc = Lib.current;
			mc.addChild(text);
		} else {
			// @todo this doens't work.
			// cam.setMode(320, 240, 30);
			// cam.setQuality(0, 100);
		}

		cam.removeEventListener(StatusEvent.STATUS, camInit);
	}
}
