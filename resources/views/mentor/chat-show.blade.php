<x-mentor.layout title="Mentorship Requests - EduConnect" active="chats">
    <x-common.chat
        :chats="$chats"
        :selected-chat="$selectedChat"
        role="mentor"
        :courses="$courses"
    />
</x-mentor.layout>