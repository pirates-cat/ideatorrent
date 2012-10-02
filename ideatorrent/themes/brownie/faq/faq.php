<?php

/*
Copyright (C) 2008 Nicolas Deschildre <ndeschildre@gmail.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//Compute the prefix path we will use
$basepath = $GLOBALS['basemodule_url'] . "/";
if($GLOBALS['gbl_relation'] != null)
{
	$basepath .= $GLOBALS['gbl_relation']->getData()->url_name . "/";
	if($GLOBALS['gbl_relationsubcat'] != null)
		$basepath .= $GLOBALS['gbl_relationsubcat']->getData()->url_name . "/";
}
if($GLOBALS['gbl_category'] != null)
	$basepath .= $GLOBALS['gbl_category']->getData()->url_name . "/";

$basepath_plus_subtabpath = $basepath . "faq/";

?>

<!-- QAPoll starts here -->
<div class="qapoll">

<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = "";
	$navbar_options["biglinks_prefix"] = $basepath;
	if($GLOBALS['gbl_relation'] != null)
	{
		$navbar_models["selected_relation"] = $GLOBALS['gbl_relation'];
		if($GLOBALS['gbl_relationsubcat'] != null)
			$navbar_models["selected_relationsubcat"] = $GLOBALS['gbl_relationsubcat'];
	}
	if($GLOBALS['gbl_category'] != null)
		$navbar_models['selected_category'] = $GLOBALS['gbl_category'];
	echo $this->loadTemplate("common/", "navigationtopbar", "", $navbar_data, $navbar_models, $navbar_options);

?>

<br />


<b class="ubuntu_title">
<b class="ubuntu_title1"><b></b></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title5"></b></b>


<table width="100%" class="ubuntu_title_main"><tr><td>
<h1 style="padding:10px 0px 0px 10px; margin: 0px 0px 0px 0px">
Questions and answers
</h1><br />

</td></tr></table>


<b class="ubuntu_title">
<b class="ubuntu_title5"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title1"><b></b></b></b>

<br />


<?php if($options["page_number"] > 0 && $options["page_number"] < 4) : ?>

<!--

//Edit your FAQ

<ul>
<li class="faq"> Ubuntu Brainstorm
<br />
<ol>
<li class="faq2"><a href="#question1">What is Ubuntu Brainstorm?</a></li>
<li class="faq2"><a href="#question2">How does it work?</a></li>
<li class="faq2"><a href="#question4">What is the "Ideas in quarantine" area?</a></li>
<li class="faq2"><a href="#question5">What is the "Popular ideas" area?</a></li>
<li class="faq2"><a href="#question6">What is the "Ideas in development" area?</a></li>
<li class="faq2"><a href="#question7">What is the "Implemented ideas" area?</a></li>
<li class="faq2"><a href="#question3">Who can participate?</a></li>
</ol>
</li>
<li class="faq"> Submitting your idea
<br />
<ol>
<li class="faq2"><a href="#question11">What are the idea posting guidelines?</a></li>
<li class="faq2"><a href="#question12">I have a package request. Is Ubuntu Brainstorm the right place?</a></li>
<li class="faq2"><a href="#question13">I want to report something that is not working as expected. Is Ubuntu Brainstorm the right place?</a></li>
<li class="faq2"><a href="#question14">I submitted my idea, and it went into the "Ideas in quarantine" area. Why?</a></li>
<li class="faq2"><a href="#question15">How does the ideas in quarantine go to the "popular ideas" area?</a></li>
<li class="faq2"><a href="#question16">My idea dissapeared from the "quarantine" area. How do I know where it went?</a></li>
<li class="faq2"><a href="#question17">My idea was marked as "Not an idea". Why?</a></li>
<li class="faq2"><a href="#question18">My idea was marked as "Already implemented". Why?</a></li>
<li class="faq2"><a href="#question19">My idea was marked as "Duplicate". Why?</a></li>
</ol>
</li>

<li class="faq"> Submitting a solution to an existing idea
<br />
<ol>
<li class="faq2"><a href="#question21">Why would I do that?</a></li>
<li class="faq2"><a href="#question22">How do I do that?</a></li>
<li class="faq2"><a href="#question23">A solution I submitted dissapeared. What happened?</a></li>
</ol>
</li>

<li class="faq"> Voting for solutions
<br />
<ol>
<li class="faq2"><a href="#question31">How should I vote?</a></li>
<li class="faq2"><a href="#question32">Can I change my vote?</a></li>
<li class="faq2"><a href="#question33">Can I vote for all the solutions?</a></li>
<li class="faq2"><a href="#question34">How do I see the vote repartion and count?</a></li>
</ol>
</li>


<li class="faq"> Notifying moderators about spam/irrelevance/ideas already implemented
<br />
<ol>
<li class="faq2"><a href="#question41">How do I report already implemented ideas?</a></li>
<li class="faq2"><a href="#question42">How do I report an offensive or irrelevant solution?</a></li>
<li class="faq2"><a href="#question43">How do I report an offensive or spam comment?</a></li>
</ol>
</li>

<li class="faq"> Developer feedback
<br />
<ol>
<li class="faq2"><a href="#question51">When does ideas get reviewed by developers?</a></li>
<li class="faq2"><a href="#question53">Where does developers comment ideas?</a></li>
<li class="faq2"><a href="#question54">There are thousands of ideas, but only a very few implemented! Why so?</a></li>
<li class="faq2"><a href="#question55">I don't see lots of developer feedback! Are developer really reviewing ideas?</a></li>
</ol>
</li>

<li class="faq"> Upstream projects
<br />
<ol>
<li class="faq2"><a href="#question61">I'm a project developer and I want to add my project and/or set custom categories. How do I do that?</a></li>
<li class="faq2"><a href="#question62">I want to fetch data from Ubuntu Brainstom. How do I do that?</a></li>
</ol>
</li>

</ul>


<div style="border-bottom: 1px dotted rgb(210, 210, 210); width: 100%;"></div>

<br />


<li class="faqanswer"> Ubuntu Brainstorm
<br />
<ol>
<li class="faqanswer2"><a name="question1"></a>What is Ubuntu Brainstorm?
<div class="faqanswer2item">
It is a place that let people brainstorm constructive ideas about Ubuntu, and evaluate each other. Popular ideas are then reviewed by Ubuntu developers, and may have an impact on the future of Ubuntu.
</div>
</li>
<li class="faqanswer2"><a name="question2"></a>How does it work?
<div class="faqanswer2item">
An idea is the combinaison of a rationale (the raison d'être of the idea) and a solution (the proposed way to solve the problem). Users can either post brand new ideas, or propose solutions to existing idea rationales. Solutions can then be evaluated by everyone by voting for or against them, and the most popular ones get reviewed by Ubuntu developers.
</div>
</li>
<li class="faqanswer2"><a name="question4"></a>What is the "Ideas in quarantine" area?
<div class="faqanswer2item">
This area contains all the ideas that were marked by moderators as not following the guidelines, plus the newly submitted ideas that are awaiting moderator approval before going to the "popular ideas" area.
</div>
</li><li class="faqanswer2"><a name="question5"></a>What is the "Popular ideas" area?
<div class="faqanswer2item">
This area contains all the approved ideas submitted by users. On this area only can ideas be voted. The most popular ideas usually get some feedback from the developers.
</div>
</li><li class="faqanswer2"><a name="question6"></a>What is the "Ideas in development" area?
<div class="faqanswer2item">
This area contains all the ideas that have been marked as being in development by developers and moderators. They can be browsed by target release.
</div>
</li><li class="faqanswer2"><a name="question7"></a>What is the "Implemented ideas" area?
<div class="faqanswer2item">
This area contains all the ideas that have been marked as implemented by the developers and moderators. They can be browsed by target release.
</div>
</li>
<li class="faqanswer2"><a name="question3"></a>Who can participate?
<div class="faqanswer2item">
Anyone can participate! Just <a href="<?php echo $GLOBALS['basemodule_url']; ?>/user/register">register</a>, and you can start posting your ideas right away!
</div>
</li>
</ol>
</li>

<li class="faqanswer"> Submitting your idea
<br />
<ol>
<li class="faqanswer2"><a name="question11"></a>What are the idea posting guidelines?
<a name="question11"></a>

<ol style="font-weight:normal">
<li><b>Really an idea?</b>
<br />
If your request concerns software that does not work correctly, incorrect translation, you should open <a href="https://bugs.launchpad.net/ubuntu/+filebug">a bug report</a>. If your request concerns a new package to be included in Ubuntu, you should use the <a href="https://wiki.ubuntu.com/UbuntuDevelopment/NewPackages">new package requesting guide</a>. Brainstorm is designed to vote and discuss ideas only.</li>

<li><b>Avoid duplicates</b>
<br />You might often find that someone has had the same idea as you before. Before posting, use the search to check if your idea has already been submitted by someone else.</li>

<li><b>Be precise</b>
<br />An idea should focus on one point only. Don't put a broad scope to your ideas or post multiple ideas at the same time. Ideas such as “Improve Ubuntu performance”, are too generic and obvious. A good example of a precise idea would be, “Add ability to queue torrents in Transmission”.</li>

<li><b>Be clear and elaborate</b>
<br />A well explained and structured idea has more chance of attracting votes.</li>

<li><b>Be respectful</b>
<br />Do not use ideas to denigrate/attack projects or people. Your idea may be deleted.</li>
</ol>


</li>
<li class="faqanswer2"><a name="question12"></a>I have a package request. Is Ubuntu Brainstorm the right place?
<div class="faqanswer2item">
No. See the <a href="https://wiki.ubuntu.com/UbuntuDevelopment/NewPackages">new package requesting guide</a>.
</div>
</li>
<li class="faqanswer2"><a name="question13"></a>I want to report something that is not working as expected. Is Ubuntu Brainstorm the right place?
<div class="faqanswer2item">
What you are describing is a bug. Brainstorm is <b>not</b> the right place. You should file a bug report on <a href="https://launchpad.net/ubuntu/+filebug">Launchpad</a>, the Ubuntu bug tracker.
</div>
</li>
<li class="faqanswer2"><a name="question14"></a>I submitted my idea, and it went into the "Ideas in quarantine" area. Why?
<div class="faqanswer2item">
Newly submitted ideas will need moderator approvals before joining the "popular ideas" area. Indeed, our experience with the first 6 months of Ubuntu  Brainstorm showed that often new ideas does not follow the basic <a href="#question11">posting guidelines</a>: lots of bugs and duplicates used to be submitted.
<br />
So basically, if your idea follow these guidelines, it will be accepted right away.
</div>
</li>
<li class="faqanswer2"><a name="question15"></a>How does the ideas in quarantine go to the "popular ideas" area?
<div class="faqanswer2item">
A given number of moderator approvals is required. You can see the number of approval your idea got in the top right corner box.
</div>
</li>
<li class="faqanswer2"><a name="question16"></a>My idea dissapeared from the "quarantine" area. How do I know where it went?
<div class="faqanswer2item">
The simplest way is to go to the "your ideas" section of your dashboard, and to look at the new status of your idea. If the status is "new", then it reached the "popular ideas" area : you should see it in the <a href="<?php echo $GLOBALS['basemodule_url']; ?>/latest_ideas/">latest popular ideas</a> list.
</div>
</li>
<li class="faqanswer2"><a name="question17"></a>My idea was marked as "Not an idea". Why?
<div class="faqanswer2item">
Your idea is probably not following the <a href="#question11">posting guidelines</a>. Be sure to check them again.
</div>
</li>
<li class="faqanswer2"><a name="question18"></a>My idea was marked as "Already implemented". Why?
<div class="faqanswer2item">
It seems you are proposing something that have already been solved. It's then no use to post it on Ubuntu Brainstorm.
</div>
</li>
<li class="faqanswer2"><a name="question19"></a>My idea was marked as "Duplicate". Why?
<div class="faqanswer2item">
Your idea has already been proposed by someone else! Your idea has thus been closed, and the votes have been reported to the other idea.
</div>
</li>
</ol>
</li>

<li class="faqanswer"> Submitting a solution to an existing idea
<br />
<ol>
<li class="faqanswer2"><a name="question21"></a>Why would I do that?
<div class="faqanswer2item">
You may have in mind a best way to solve a rationale submitted by another user. If so, don't hesitate to propose your solution!
</div>
</li>
<li class="faqanswer2"><a name="question22"></a>How do I do that?
<div class="faqanswer2item">
On the idea page, use the "propose your solution" link.
</div>
</li>
<li class="faqanswer2"><a name="question23"></a>A solution I submitted dissapeared. What happened?
<div class="faqanswer2item">
It is likely your solution was either a duplicate of another, in which case the votes should have been merged, or it was not relevant to the idea rationale.
</div>
</li>
</ol>
</li>

<li class="faqanswer"> Voting for solutions
<br />
<ol>
<li class="faqanswer2"><a name="question31"></a>How should I vote?
<div class="faqanswer2item">
Keep in mind that you should vote in accordance to <b>your</b> opinion to the solution.
If you feel a solution is a good or bad way to solve the idea rationale, use the up or down arrows. But if you have a mixed feeling, or if you don't care about the problem the idea is trying to solve, just cast a blank vote by using the orange square.
</div>
</li>
<li class="faqanswer2"><a name="question32"></a>Can I change my vote?
<div class="faqanswer2item">
Yes. Just click on the greyed voting images.
</div>
</li>
<li class="faqanswer2"><a name="question33"></a>Can I vote on all the solutions?
<div class="faqanswer2item">
Yes, you are encouraged to do so.
</div>
</li>
<li class="faqanswer2"><a name="question34"></a>How do I see the vote repartion and count?
<div class="faqanswer2item">
Yes. The bar above the voting arrows represent the vote repartition, and if you put your cursor on top of it, a tooltip with the number of votes will appear.
</div>
</li>
</ol>
</li>


<li class="faqanswer"> Notifying moderators about spam/irrelevance/ideas already implemented
<br />
<ol>
<li class="faqanswer2"><a name="question41"></a>How do I report already implemented ideas?
<div class="faqanswer2item">
Go to the idea page, and just above the idea rationale, you should find links to report an idea as already implemented, but also in development. Please note that you need to be logged in.
</div>
</li>
<li class="faqanswer2"><a name="question42"></a>How do I report an offensive or irrelevant solution?
<div class="faqanswer2item">
You shall find report links under solution title. Please note that you need to be logged in.
</div>
</li>
<li class="faqanswer2"><a name="question43"></a>How do I report an offensive or spam comment?
<div class="faqanswer2item">
You shall find report links on the top right corner of comments. Please note that you need to be logged in.
</div>
</li>
</ol>
</li>

<li class="faqanswer"> Developer feedback
<br />
<ol>
<li class="faqanswer2"><a name="question51"></a>When does ideas get reviewed by developers?
<div class="faqanswer2item">
The most popular ideas are regularly reviewed by developers, especially the month after a release, when it's time to plan the next features to be worked on the next 6 months. Reviews can include a developer comment in the idea page.
</div>
</li>
<li class="faqanswer2"><a name="question53"></a>Where does developers comment ideas?
<div class="faqanswer2item">
The developer comments are shown in a brown box both in the idea lists and individual idea pages. Sometimes, when a developer comment is quite exhaustive, it is posted separately on the <a href="http://blog.qa.ubuntu.com/">Ubuntu QA blog</a>.
</div>
</li>
<li class="faqanswer2"><a name="question54"></a>There are thousands of ideas, but only a very few implemented! Why so?
<div class="faqanswer2item">
When developing a feature, we want to make sure it will be rock solid and work on all the different possible hardware and software configurations. Thus, the time required to develop a feature can be much longer than one can expect.
</div>
</li>
<li class="faqanswer2"><a name="question54"></a>I don't see lots of developer feedback! Are developer really reviewing ideas?
<div class="faqanswer2item">
Yes. But keep in mind that the main activity of developer is ... developing! So while they keep an eye on Ubuntu Brainstorm, their main preoccupation will be to finish in time the job assigned to them for the next release. The period you are likely to find the most interactions of developers on Ubuntu Brainstorm is during the month after the release is out, on the feature planning period.
</div>
</li>
</ol>
</li>


<li class="faqanswer"> Upstream projects
<br />
<ol>
<li class="faqanswer2"><a name="question61"></a>I'm a project developer and I want to add my project and/or set custom categories. How do I do that?
<div class="faqanswer2item">
No problem! Submit your request on the mailing list <a href="https://launchpad.net/~brainstorm-moderators">here</a>.
</div>
</li>
<li class="faqanswer2"><a name="question62"></a>I want to fetch data from Ubuntu Brainstom. How do I do that?
<div class="faqanswer2item">
XML sources of all categories and projects are available. Just happen "/xml" on the project URL. For example, if you want to get the XML data of the Amarok ideas, fetch the XML from <a href="<?php echo $GLOBALS['basemodule_url']; ?>/amarok/xml"><?php echo $GLOBALS['basemodule_url']; ?>/amarok/xml</a>.
</div>
</li>
</ol>
</li>

-->


<?php endif; ?>


</div>
<!-- QAPoll ends here -->
