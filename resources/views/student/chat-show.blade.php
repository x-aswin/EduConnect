<x-mentor.layout title="Mentorship Requests - EduConnect" active="chat">
    <x-common.chat
        :chats="$chats"
        :selected-chat="$selectedChat"
        role="student"
        :courses="$courses"
    />
</x-mentor.layout>