import flash.events.MouseEvent;
import flash.Lib;
import flash.display.Bitmap;
import flash.display.BitmapData;
import flash.display.MovieClip;
import flash.media.Video;
import flash.media.Camera;
import flash.events.StatusEvent;
import flash.external.ExternalInterface;

import flash.geom.Rectangle;
import flash.utils.ByteArray;

import haxe.crypto.Base64;

import flash.text.TextField;
import flash.text.TextFieldAutoSize;

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

	public function new() {
		if (cam != null) {
			// set sizes
			cam.addEventListener(StatusEvent.STATUS, camInit);

			mc = Lib.current;
			vid = new Video(cam.width, cam.height);
			vid.attachCamera(cam);

			// have to have a container to attach a click event.
			vidContainer = new MovieClip();
			vidContainer.addChild(vid);
			mc.addChild(vidContainer);

			// pause / unpause on click
			vidContainer.addEventListener(MouseEvent.CLICK, function(e:MouseEvent) {
				if (!hasSnap) {
					var bitmapData: BitmapData = new BitmapData(cam.width, cam.height);
					var bitmap: Bitmap = new Bitmap(bitmapData);

					// place over playing video
					bitmap.x = 0;
					bitmap.y = 0;
					bitmap.name = "snap";

					mc.addChild(bitmap);
					bitmapData.draw(vid);
					hasSnap = true;

					var byteArray:ByteArray = new ByteArray();
					byteArray = bitmapData.encode(new Rectangle(0, 0, cam.width, cam.height), new flash.display.JPEGEncoderOptions()); 
					var base64 = Base64.encode(haxe.io.Bytes.ofData(byteArray));
					ExternalInterface.call('elgg.avatar.saveBase64Input', base64, '.elgg-form-avatar-upload');
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
			cam.setMode(320, 240, 30);
			cam.setQuality(0, 100);
		}
	}

	public function displaySnap() {
		trace("Snapping");
	}
}

/*
stage.align = StageAlign.TOP_LEFT;
stage.scaleMode = StageScaleMode.NO_SCALE;
//right click credits menu
var rightClickMenu: ContextMenu = new ContextMenu();
var copyright: ContextMenuItem = new ContextMenuItem("Made by Centillien, Business Social Network");
copyright.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, myLink);
copyright.separatorBefore = false;
rightClickMenu.hideBuiltInItems();
rightClickMenu.customItems.push(copyright);
this.contextMenu = rightClickMenu;
function myLink(e: Event) {
	navigateToURL(new URLRequest("http://www.centillien.com"), "_blank");
}


var snd: Sound = new camerasound(); //new sound instance for the "capture" button click

var bandwidth: int = 0; // Maximum amount of bandwidth that the current outgoing video feed can use, in bytes per second.
var quality: int = 100; // This value is 0-100 with 1 being the lowest quality.

var cam: Camera = Camera.getCamera();
cam.setQuality(bandwidth, quality);

// setMode(videoWidth, videoHeight, video fps, favor area)
//cam.setMode(320,240,30,false);
cam.setMode(200, 200, 30, false);

var video: Video = new Video(200, 200);
video.attachCamera(cam);
video.x = 100;
video.y = 20;
addChild(video);

var bitmapData: BitmapData = new BitmapData(video.width, video.height);

var bitmap: Bitmap = new Bitmap(bitmapData);
bitmap.x = 360;
bitmap.y = 20;
addChild(bitmap);

capture_mc.buttonMode = true;
capture_mc.addEventListener(MouseEvent.CLICK, captureImage);

function captureImage(e: MouseEvent): void {
	snd.play();
	bitmapData.draw(video);
	save_mc.buttonMode = true;
	save_mc.addEventListener(MouseEvent.CLICK, onSaveJPG);
	save_mc.alpha = 1;
}

save_mc.alpha = .5;

function onSaveJPG(e: Event): void {
	capture_mc.enabled = false;
	capture_mc.alpha = .5;
	save_mc.enabled = false;
	save_mc.alpha = .5;

	var myEncoder: JPGEncoder = new JPGEncoder(100);
	var byteArray: ByteArray = myEncoder.encode(bitmapData);

	var handlePage: String = "/mod/webcam/actions/save.php";
	var req: URLRequest = new URLRequest(handlePage);

	req.method = URLRequestMethod.POST;
	req.data = byteArray;
	req.contentType = "application/octet-stream";
	//sendToURL(req);

	navigateToURL(req, "_self");
	capture_mc.enabled = true;
	capture_mc.alpha = 1;
	save_mc.enabled = true;
	save_mc.alpha = 1;
	
//    var loader:URLLoader = new URLLoader();
//    loader.addEventListener(Event.COMPLETE, completeHandler);
//    loader.addEventListener(IOErrorEvent.IO_ERROR,errorHandler);
//    loader.load(req);
}

function completeHandler(event: Event): void {
	trace("Error");
	capture_mc.enabled = true;
	capture_mc.alpha = 1;
	save_mc.enabled = true;
	save_mc.alpha = 1;
}

function errorHandler(event: IOErrorEvent): void {
	trace("Error");
	capture_mc.enabled = true;
	capture_mc.alpha = 1;
	save_mc.enabled = true;
	save_mc.alpha = 1;
}
*/