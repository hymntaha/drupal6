/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License version 3 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
 * Last change : $Date: 2010-04-28 19:32:08 +0000 (Wed, 28 Apr 2010) $ by $Author: foux $
 * Revision : $Revision: 234862 $
 */

function categorieCheckBoxChanges(categorieId) {
	var templateList = document.getElementsByName("wpPingCat" + categorieId)[0];
	var checkbox = document.getElementsByName("wpPingEnableCat" + categorieId)[0];
	var postUpdatesCheckbox = document.getElementsByName("wpPingCatRepub" + categorieId)[0];
	var updatesTemplateList = document.getElementsByName("wpPingCatRepubTemplate" + categorieId)[0];
	var divGlob = document.getElementById("showHide" + categorieId);
	var divUpdate = document.getElementById("showHideRepub" + categorieId);
	var divUpdateTemplate = document.getElementById("showHideRepubTemp" + categorieId);
	if (checkbox.checked) {
		templateList.disabled = false;
		divGlob.style.visibility = "visible";
		divUpdate.style.visibility = "visible";
		if (postUpdatesCheckbox.checked) {
			divUpdateTemplate.style.visibility = "visible";
			updatesTemplateList.disabled = false;
		} else {
			divUpdateTemplate.style.visibility = "hidden";
			updatesTemplateList.disabled = true;
		}
	} else {
		templateList.disabled = true;
		updatesTemplateList.disabled = true;
		divGlob.style.visibility = "hidden";
		divUpdate.style.visibility = "hidden";
		divUpdateTemplate.style.visibility = "hidden";
	}
}