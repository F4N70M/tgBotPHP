<?php

namespace Bot\i;

interface iApi
{
	public function __construct(string $token);

	// public function getUpdate();
	public function call(string $method, array $data = []);


	// Available methods

	// public function getMe();
	// public function logOut();
	// public function close();
	// public function sendMessage();
	// public function forwardMessage();
	// public function copyMessage();
	// public function sendPhoto();
	// public function sendAudio();
	// public function sendDocument();
	// public function sendVideo();
	// public function sendAnimation();
	// public function sendVoice();
	// public function sendVideoNote();
	// public function sendMediaGroup();
	// public function sendLocation();
	// public function sendVenue();
	// public function sendContact();
	// public function sendPoll();
	// public function sendDice();
	// public function sendChatAction();
	// public function getUserProfilePhotos();
	// public function getFile();
	// public function banChatMember();
	// public function unbanChatMember();
	// public function restrictChatMember();
	// public function promoteChatMember();
	// public function setChatAdministratorCustomTitle();
	// public function banChatSenderChat();
	// public function unbanChatSenderChat();
	// public function setChatPermissions();
	// public function exportChatInviteLink();
	// public function createChatInviteLink();
	// public function editChatInviteLink();
	// public function revokeChatInviteLink();
	// public function approveChatJoinRequest();
	// public function declineChatJoinRequest();
	// public function setChatPhoto();
	// public function deleteChatPhoto();
	// public function setChatTitle();
	// public function setChatDescription();
	// public function pinChatMessage();
	// public function unpinChatMessage();
	// public function unpinAllChatMessages();
	// public function leaveChat();
	// public function getChat();
	// public function getChatAdministrators();
	// public function getChatMemberCount();
	// public function getChatMember();
	// public function setChatStickerSet();
	// public function deleteChatStickerSet();
	// public function getForumTopicIconStickers();
	// public function createForumTopic();
	// public function editForumTopic();
	// public function closeForumTopic();
	// public function reopenForumTopic();
	// public function deleteForumTopic();
	// public function unpinAllForumTopicMessages();
	// public function editGeneralForumTopic();
	// public function closeGeneralForumTopic();
	// public function reopenGeneralForumTopic();
	// public function hideGeneralForumTopic();
	// public function unhideGeneralForumTopic();
	// public function answerCallbackQuery();
	// public function setMyCommands();
	// public function deleteMyCommands();
	// public function getMyCommands();
	// public function setMyName();
	// public function getMyName();
	// public function setMyDescription();
	// public function getMyDescription();
	// public function setMyShortDescription();
	// public function getMyShortDescription();
	// public function setChatMenuButton();
	// public function getChatMenuButton();
	// public function setMyDefaultAdministratorRights();
	// public function getMyDefaultAdministratorRights();


	// Updating messages

	// public function editMessageText();
	// public function editMessageCaption();
	// public function editMessageMedia();
	// public function editMessageLiveLocation();
	// public function stopMessageLiveLocation();
	// public function editMessageReplyMarkup();
	// public function stopPoll();
	// public function deleteMessage();

}